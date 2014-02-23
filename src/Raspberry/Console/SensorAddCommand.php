<?php

namespace Raspberry\Console;

use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
class SensorAddCommand extends Command {

	/**
	 * @var SensorGateway
	 */
	private $_sensor_gateway;

	/**
	 * @var SensorBuilder
	 */
	private $_sensor_builder;

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('sensor:add')->setDescription('Add a new Sensor');
	}

	/**
	 * @Inject({"@SensorGateway", "@SensorBuilder"})
	 */
	public function setDependencies(SensorGateway $sensor_gateway, SensorBuilder $sensor_builder) {
		$this->_sensor_gateway = $sensor_gateway;
		$this->_sensor_builder = $sensor_builder;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		/** @var DialogHelper $dialog */
		$dialog = $this->getHelperSet()->get('dialog');

		$sensor_types = array_keys($this->_sensor_builder->getSensors());

		$sensor_type_idx = $dialog->select($output, "Sensor type?\n", $sensor_types);
		$sensor_type = $sensor_types[$sensor_type_idx];

		$sensor = $this->_sensor_builder->build($sensor_type);
		if (!$sensor->isSupported($output)) {
			$output->writeln('<error>Sensor is not supported</error>');
			$this->_askForTermination($dialog, $output);
		} else {
			$output->writeln('<info>Sensor is supported</info>');
		}

		$name = $dialog->ask($output, "Sensor name\n");
		$description = $dialog->ask($output, "Description (optional)\n");
		$pin = $dialog->ask($output, "Pin (optional)\n");
		$interval = (int)$dialog->ask($output, "Interval in minutes\n") ?: 1;

		// get test value
		$test_value = $sensor->getValue($pin);
		if ($test_value !== null) {
			$output->writeln(sprintf('<info>Sensor value: %s</info>', $sensor->formatValue($test_value)));
		} else {
			$output->writeln('<error>Sensor returned invalid data.</error>');
			$this->_askForTermination($dialog, $output);
		}

		$this->_sensor_gateway->addSensor($name, $sensor_type, $description, $pin, $interval);
	}

	/**
	 * @param DialogHelper $dialog
	 * @param OutputInterface $output
	 */
	private function _askForTermination(DialogHelper $dialog, OutputInterface $output) {
		if ($dialog->askConfirmation($output, 'Abort adding this sersor? (y/n)')) {
			exit(1);
		}
	}

} 