<?php

namespace Homie\TodoList\Cron;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Traits\LoggerTrait;
use BrainExe\Core\Traits\TimeTrait;
use Cron\CronExpression;
use Homie\Sensors\GetValue\Event;
use Homie\Sensors\SensorBuilder;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorValueEvent;
use Homie\Sensors\Builder;
use Homie\Sensors\SensorVO;
use Homie\TodoList\TodoList;
use Homie\TodoList\TodoListGateway;
use Homie\TodoList\VO\TodoItemVO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation("TodoList.Cron.UpdateTask")
 */
class UpdateTasks extends Command
{
    use TimeTrait;

    /**
     * @var TodoListGateway
     */
    private $gateway;

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
     * @Inject({
     *  "@TodoListGateway",
     *  "@TodoList",
     * })
     * @param TodoListGateway $gateway
     * @param TodoList $todoList
     */
    public function __construct(
        TodoListGateway $gateway,
        TodoList $todoList
    ) {

        $this->gateway  = $gateway;
        $this->todoList = $todoList;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $now = $this->now();

        $tasks = $this->todoList->getList();

        foreach ($tasks as $task) {
            if ($task->status = TodoItemVO::STATUS_PENDING && $task->cronExpression) {
                $cron = CronExpression::factory($task->cronExpression);
                $nextChange = $cron->getNextRunDate($task->lastChange);

                if ($nextChange < $now) {
                    $task->status = TodoItemVO::STATUS_OPEN;
                    $this->todoList->editItem($task->todoId, [
                        'status' => $task->status
                    ]);
                }
            }
        }

    }
}
