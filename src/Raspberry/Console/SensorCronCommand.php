<?php

namespace Raspberry\Console;

use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

/**
 * @DI\Service(public=false, tags={{"name" = "console"}})
 */
class SensorCronCommand extends Command {

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
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
			->setName('cron:sensor')
			->setDescription('Runs sensor cron');
	}

	/**
	 * @DI\Inject({"@SensorGateway", "@SensorValuesGateway", "@SensorBuilder"})
	 */
	public function setDependencies(SensorGateway $sensor_gateway, SensorValuesGateway $sensor_values_gateway, SensorBuilder $sensor_builder) {
		$this->_sensor_builder = $sensor_builder;
		$this->_sensor_gateway = $sensor_gateway;
		$this->_sensor_values_gateway = $sensor_values_gateway;
	}
	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$minute = date('i');
		$sensors = $this->_sensor_gateway->getSensors();

		foreach ($sensors as $sensor_data) {
			$interval = $sensor_data['interval'];
			if ($minute % $interval === 0) {
				$sensor = $this->_sensor_builder->build($sensor_data);

				$value = $sensor->getValue($sensor_data['pin']);

				if ($value === null) {
					$output->writeln(sprintf('<error>Invalid sensor value: #%d %s (%s)</error>', $sensor_data['id'], $sensor_data['type'], $sensor_data['name']));
					continue;
				}

				$this->_sensor_values_gateway->addValue($sensor_data['id'], $value);

				$output->writeln(sprintf('<info>Sensor value: #%d %s (%s): %s</info>', $sensor_data['id'], $sensor_data['type'], $sensor_data['name'], $sensor->formatValue($value)));

				sleep(1);
			}

			if ($minute == 0 && date('G') == 0) {
				$this->_sensor_values_gateway->deleteOldValues(1, 25);
				$this->_sensor_values_gateway->deleteOldValues(3, 50);
				$this->_sensor_values_gateway->deleteOldValues(5, 75);
				$this->_sensor_values_gateway->deleteOldValues(10, 90);
			}
		}
	}

} 