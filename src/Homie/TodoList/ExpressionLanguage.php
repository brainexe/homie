<?php

namespace Homie\TodoList;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Annotations\Annotations\Inject;
use Generator;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @Service("TodoList.ExpressionLanguage", public=false)
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{

    /**
     * @var TodoReminder
     */
    private $todoReminder;

    /**
     * @Inject("@TodoList.TodoReminder")
     * @param TodoReminder $todoReminder
     */
    public function __construct(TodoReminder $todoReminder)
    {
        $this->todoReminder = $todoReminder;
    }

    /**
     * @return Generator
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('sayTodoList', function () {
            throw new InvalidArgumentException('Function sayTodoList() is not implemented as trigger');
        }, function () {
            $this->todoReminder->sendNotification();
        });
    }
}
