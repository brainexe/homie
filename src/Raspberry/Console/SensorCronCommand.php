<?php

namespace Raspberry\Console;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Traits\LoggerTrait;
use BrainExe\Core\Traits\TimeTrait;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValueEvent;
use Raspberry\Sensors\SensorValuesGateway;
use Raspberry\Sensors\SensorVOBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
class SensorCronCommand extends Command {

	use LoggerTrait;
	use TimeTrait;

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
	 * @param SensorGateway $sensor_gateway
	 * @param SensorValuesGateway $sensor_values_gateway
	 * @param SensorBuilder $sensor_builder
	 * @param SensorVOBuilder $sensor_vo_builder
	 * @param EventDispatcher $event_dispatcher
	 * @param integer $node_id
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
		$now = $this->now();
		$sensors = $this->_sensor_gateway->getSensors($this->_node_id);

		foreach ($sensors as $sensor_data) {
			$sensor_vo = $this->_sensor_vo_builder->buildFromArray($sensor_data);

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

				$formatted_sensor_value = $sensor->formatValue($current_sensor_value);
				$event = new SensorValueEvent(
					$sensor_vo,
					$sensor,
					$current_sensor_value,
					$formatted_sensor_value,
					$now
				);
				$this->_event_dispatcher->dispatchEvent($event);

				$this->_sensor_values_gateway->addValue($sensor_vo->id, $current_sensor_value);

				$output->writeln(sprintf('<info>Sensor value: #%d %s (%s): %s</info>', $sensor_vo->id, $sensor_vo->type, $sensor_vo->name, $formatted_sensor_value));
			}
		}
	}

} 
