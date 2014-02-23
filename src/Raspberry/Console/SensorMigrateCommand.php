<?php

namespace Raspberry\Console;

use Raspberry\Sensors\MigrateGateway;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
class SensorMigrateCommand extends Command {

	/**
	 * @var MigrateGateway
	 */
	private $_migrate;

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('sensor:migrate');
	}

	/**
	 * @Inject("@MigrateGateway")
	 */
	public function setDependencies(MigrateGateway $migrate) {
		$this->_migrate = $migrate;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->_migrate->migrateSensors();
	}

} 