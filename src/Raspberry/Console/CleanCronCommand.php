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
class CleanCronCommand extends Command {

	/**
	 * @var SensorValuesGateway
	 */
	private $_sensor_values_gateway;

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
			->setName('cron:clean')
			->setDescription('Delete old sensor values');
	}

	/**
	 * @DI\Inject({"@SensorValuesGateway"})
	 */
	public function setDependencies(SensorValuesGateway $sensor_values_gateway) {
		$this->_sensor_values_gateway = $sensor_values_gateway;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->_sensor_values_gateway->deleteOldValues(1, 25);
		$this->_sensor_values_gateway->deleteOldValues(3, 50);
		$this->_sensor_values_gateway->deleteOldValues(5, 75);
		$this->_sensor_values_gateway->deleteOldValues(10, 90);

		$output->writeln('<info>done</info>');
	}

} 