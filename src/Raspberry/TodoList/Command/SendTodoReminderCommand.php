<?php

namespace Raspberry\TodoList\Command;

use BrainExe\Core\Console\AbstractCommand;
use Raspberry\TodoList\TodoReminder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
class SendTodoReminderCommand extends AbstractCommand {

	/**
	 * @var TodoReminder
	 */
	private $_todoReminder;

	/**
	 * @inject("@TodoReminder")
	 * @param TodoReminder $todoReminder
	 */
	public function __construct(TodoReminder $todoReminder) {
		$this->_todoReminder = $todoReminder;
		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
			->setName('todo:reminder')
			->setDescription('Todo reminder');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output) {
		$this->_todoReminder->sendNotification();
	}
} 