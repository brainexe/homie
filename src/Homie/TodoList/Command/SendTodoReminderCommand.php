<?php

namespace Homie\TodoList\Command;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Command as CommandAnnotation;
use Homie\TodoList\TodoReminder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @CommandAnnotation
 */
class SendTodoReminderCommand extends Command
{

    /**
     * @var TodoReminder
     */
    private $todoReminder;

    /**
     * @Inject("@TodoReminder")
     * @param TodoReminder $todoReminder
     */
    public function __construct(TodoReminder $todoReminder)
    {
        $this->todoReminder = $todoReminder;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('todo:reminder')
            ->setDescription('Todo reminder');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->todoReminder->sendNotification();
    }
}
