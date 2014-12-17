<?php

namespace Raspberry\Console;

use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
class CleanCronCommand extends Command {

	/**
	 * @var SensorValuesGateway
	 */
	private $sensorValuesGateway;

	/**
	 * @var SensorGateway
	 */
	private $sensorGateway;

	/**
	 * @var array
	 */
	private $valueDeleteSensorValues;

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('cron:clean')
			->setDescription('Delete old sensor values');
	}

	/**
	 * @Inject({"@SensorValuesGateway", "@SensorGateway", "%delete_sensor_values%"})
	 * @param SensorValuesGateway $sensor_values_gateway
	 * @param SensorGateway $sensor_gateway
	 * @param integer[] $delete_sensor_values
	 */
	public function __construct(SensorValuesGateway $sensor_values_gateway, SensorGateway $sensor_gateway, $delete_sensor_values) {
		$this->sensorValuesGateway = $sensor_values_gateway;
		$this->valueDeleteSensorValues = $delete_sensor_values;
		$this->sensorGateway = $sensor_gateway;

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$sensor_ids = $this->sensorGateway->getSensorIds();

		foreach ($sensor_ids as $sensor_id) {
			$this->_deleteOldValues($output, $sensor_id);
		}

		$output->writeln('<info>done</info>');
	}

	/**
	 * @param OutputInterface $output
	 * @param integer $sensor_id
	 */
	private function _deleteOldValues(OutputInterface $output, $sensor_id) {
		$deleted_rows = 0;

		foreach ($this->valueDeleteSensorValues as $delete) {
			$deleted_rows += $this->sensorValuesGateway->deleteOldValues(
				$sensor_id,
				$delete['days'],
				$delete['percentage']
			);
		}

		$output->writeln(sprintf('<info>sensor #%d, deleted %d rows</info>', $sensor_id, $deleted_rows));
	}

}
