<?php

namespace Raspberry\Console;

use Matze\Core\Traits\LoggerTrait;
use Raspberry\Radio\RadioJob;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
class SensorCronCommand extends Command {

	use LoggerTrait;

	/**
	 * @var SensorGateway
	 */
	private $_sensor_gateway;

	/**
	 * @var SensorValuesGateway
	 */
	private $_sensor_values_gateway;

	/**
	 * @var SensorBuilder
	 */
	private $_sensor_builder;

	/**
	 * @var RadioJob
	 */
	private $_radio_job;

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('cron:sensor')
			->setDescription('Runs sensor cron')
			->addOption('force', null, InputOption::VALUE_NONE, 'Force sensor mesasure');
	}

	/**
	 * @Inject({"@SensorGateway", "@SensorValuesGateway", "@SensorBuilder", "@RadioJob"})
	 */
	public function __construct(SensorGateway $sensor_gateway, SensorValuesGateway $sensor_values_gateway, SensorBuilder $sensor_builder, RadioJob $radio_job) {
		$this->_sensor_builder = $sensor_builder;
		$this->_sensor_gateway = $sensor_gateway;
		$this->_sensor_values_gateway = $sensor_values_gateway;
		$this->_radio_job = $radio_job;

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$now = time();
		$sensors = $this->_sensor_gateway->getSensors();

		foreach ($sensors as $sensor_data) {
			$interval = $sensor_data['interval'] ?: 1;

			$last_run_timestamp = $sensor_data['last_value_timestamp'];
			$delta = $now - $last_run_timestamp;
			if ($delta > $interval * 60 || $input->getOption('force')) {
				$sensor = $this->_sensor_builder->build($sensor_data['type']);

				$current_sensor_value = $sensor->getValue($sensor_data['pin']);
				if ($current_sensor_value === null) {
					$output->writeln(sprintf('<error>Invalid sensor value: #%d %s (%s)</error>', $sensor_data['id'], $sensor_data['type'], $sensor_data['name']));
					continue;
				}

				$this->_sensor_values_gateway->addValue($sensor_data['id'], $current_sensor_value);

				$output->writeln(sprintf('<info>Sensor value: #%d %s (%s): %s</info>', $sensor_data['id'], $sensor_data['type'], $sensor_data['name'], $sensor->formatValue($current_sensor_value)));

				usleep(500000);
			}
		}

		// TODO move into regular MQ
		$this->_radio_job->handlePendingJobs();
	}

} 
