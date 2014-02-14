<?php

namespace Raspberry\Console;

use Predis\Client;
use Raspberry\Radio\RadioController;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Matze\Annotations\Annotations as DI;

/**
 * @DI\Service(public=false, tags={{"name" = "console"}})
 */
class DeamonCommand extends Command {

	/**
	 * @var Client
	 */
	private $_predis;

	/**
	 * @var RadioController
	 */
	private $_radio_controller;

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
			->setName('deamon')
			->setDescription('Runs deamon');
	}

	/**
	 * @DI\Inject({"@RadioController", "@Predis"})
	 */
	public function setDependencies(RadioController $radio_controller, Client $predis) {
		$this->_radio_controller = $radio_controller;
		$this->_predis = $predis;
	}
	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {

		// Initialize a new pubsub context
		$pub_sub = $this->_predis->pubSubLoop();

		// Subscribe to your channels
		$pub_sub->subscribe('radio_changes');

		foreach ($pub_sub as $change) {
			if ($change->kind !== 'message') {
				continue;
			}
			$payload = unserialize($change->payload);
			if (!$payload) {
				continue;
			}

			try {
				$this->_radio_controller->setStatus($payload['code'], $payload['pin'], $payload['status']);
			} catch (RuntimeException $e) {
				echo $e->getMessage()."\n";
			}

			echo $payload['code'] ." - ". $payload['pin'] ." - ". $payload['status'] ."\n";
		}
	}

} 