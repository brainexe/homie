<?php

namespace Raspberry\Console;

use Matze\Core\Traits\LoggerTrait;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValueEvent;
use Raspberry\Sensors\SensorValuesGateway;
use Raspberry\Sensors\SensorVOBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
	 * @var integer
	 */
	private $_node_id;

	/**
	 * @var SensorVOBuilder
	 */
	private $_sensor_vo_builder;

	/**
	 * @var EventDispatcher
	 */
	private $_event_dispatcher;

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('cron:sensor')
			->setDescription('Runs sensor cron')
			->addOption('force', null, InputOption::VALUE_NONE, 'Force sensor mesasure');
	}

	/**
	 * @Inject({"@SensorGateway", "@SensorValuesGateway", "@SensorBuilder", "@SensorVOBuilder", "@EventDispatcher", "%node.id%"})
	 */
	public function __construct(SensorGateway $sensor_gateway, SensorValuesGateway $sensor_values_gateway, SensorBuilder $sensor_builder, SensorVOBuilder $sensor_vo_builder, EventDispatcher $event_dispatcher, $node_id) {
		$this->_sensor_builder = $sensor_builder;
		$this->_sensor_gateway = $sensor_gateway;
		$this->_sensor_values_gateway = $sensor_values_gateway;
		$this->_sensor_vo_builder = $sensor_vo_builder;
		$this->_event_dispatcher = $event_dispatcher;
		$this->_node_id = $node_id;

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$now = time();
		$sensors = $this->_sensor_gateway->getSensors($this->_node_id);

		foreach ($sensors as $sensor_data) {
			$sensor_vo = $this->_sensor_vo_builder->buildSensorVOFromArray($sensor_data);

			$interval = $sensor_vo->interval ?: 1;

			$last_run_timestamp = $sensor_vo->last_value_timestamp;
			$delta = $now - $last_run_timestamp;
			if ($delta > $interval * 60 || $input->getOption('force')) {
				$sensor = $this->_sensor_builder->build($sensor_vo->type);
				$current_sensor_value = $sensor->getValue($sensor_vo->pin);
				if ($current_sensor_value === null) {
					$output->writeln(sprintf('<error>Invalid sensor value: #%d %s (%s)</error>', $sensor_vo->id, $sensor_vo->type, $sensor_vo->name));
					continue;
				}

				$event = new SensorValueEvent($sensor_vo, $sensor, $current_sensor_value);
				$this->_event_dispatcher->dispatch($event->event_name, $event);

				$this->_sensor_values_gateway->addValue($sensor_vo->id, $current_sensor_value);

				$output->writeln(sprintf('<info>Sensor value: #%d %s (%s): %s</info>', $sensor_vo->id, $sensor_vo->type, $sensor_vo->name, $sensor->formatValue($current_sensor_value)));

				usleep(500000);
			}
		}
	}

} 
