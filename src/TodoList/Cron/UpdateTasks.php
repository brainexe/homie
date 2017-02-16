<?php

namespace Homie\TodoList\Cron;

use BrainExe\Annotations\Annotations\Inject;

use BrainExe\Core\Traits\TimeTrait;
use Cron\CronExpression;

use Homie\TodoList\TodoList;
use Homie\TodoList\VO\TodoItemVO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation("TodoList.Cron.UpdateTask")
 */
class UpdateTasks extends Command
{
    use TimeTrait;

    /**
     * @var TodoList
     */
    private $todoList;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('todo:update')
            ->setDescription('Update all tasks');
    }

    /**
     * @Inject
     * @param TodoList $todoList
     */
    public function __construct(
        TodoList $todoList
    ) {
        $this->todoList = $todoList;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tasks = $this->todoList->getList();

        foreach ($tasks as $task) {
            if ($task->status == TodoItemVO::STATUS_PENDING && $task->cronExpression) {
                $this->handleTask($task);
            }
        }
    }

    /**
     * @param TodoItemVO $task
     */
    protected function handleTask(TodoItemVO $task)
    {
        $cron       = CronExpression::factory($task->cronExpression);
        $nextChange = $cron->getNextRunDate($task->lastChange);

        $now = $this->now();
        if ($nextChange->getTimestamp() < $now) {
            $task->status = TodoItemVO::STATUS_OPEN;
            $this->todoList->editItem($task->todoId, [
                'status' => $task->status
            ]);
        }
    }
}
