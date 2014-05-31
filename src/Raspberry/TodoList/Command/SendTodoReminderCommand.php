<?php

namespace Raspberry\TodoList\Command;

use Matze\Core\Console\AbstractCommand;
use Matze\Core\Traits\ServiceContainerTrait;
use Raspberry\TodoList\TodoReminder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
class SendTodoReminderCommand extends AbstractCommand {

	use ServiceContainerTrait;

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
		/** @var TodoReminder $todo_reminder */
		$todo_reminder = $this->getService('TodoReminder');
		$todo_reminder->sendNotification();
	}
} 