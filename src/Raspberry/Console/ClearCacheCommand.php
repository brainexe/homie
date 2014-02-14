<?php

namespace Raspberry\Console;

use Matze\Core\Core;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Matze\Annotations\Annotations as DI;

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
		unlink('cache/dic.php');

		$output->write('Rebuild DIC...');
		Core::rebuildDIC();
		$output->writeln('<info>...done</info>');

		$output->write('Clear Twig Cache...');
		$file_system = new Filesystem();
		$file_system->remove('../cache/twig/');
		$file_system->mkdir('../cache/twig/', 0777);
		$output->writeln('<info>...done</info>');
	}

} 