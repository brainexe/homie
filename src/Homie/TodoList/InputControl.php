<?php

namespace Homie\TodoList;

use BrainExe\InputControl\Annotations\InputControlInterface;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;
use BrainExe\Annotations\Annotations\Inject;

/**
 * @InputControlAnnotation(name="TodoList")
 */
class InputControl implements InputControlInterface
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
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            '/^todo list/i' => 'notify'
        ];
    }

    public function notify()
    {
        $this->todoReminder->sendNotification();
    }
}
