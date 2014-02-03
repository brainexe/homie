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
class ClearCacheCommand extends Command {

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
			->setName('cache:clear')
			->setDescription('Clears the local cache');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		unlink('../cache/dic.php');

		rebuild_dic();

		$output->writeln('<info>done</info>');
	}

} 