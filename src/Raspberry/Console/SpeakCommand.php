<?php

namespace Raspberry\Console;

use Matze\Core\Traits\EventDispatcherTrait;
use Matze\Core\Traits\LoggerTrait;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Raspberry\Espeak\EspeakEvent;

use Raspberry\Espeak\EspeakVO;

/**
 * @Command
 */
class SpeakCommand extends Command {

	use EventDispatcherTrait;

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this->setName('speak')
			->setDescription('Speak via espeak')
			->addArgument('text');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$text = $input->getArgument('text');
		$espeak_vo = new EspeakVO($text);

		$event = new EspeakEvent($espeak_vo);

		$this->dispatchEvent($event);

	}

} 
