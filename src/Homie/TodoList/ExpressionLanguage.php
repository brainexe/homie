<?php

namespace Homie\TodoList;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Authentication\AnonymusUserVO;
use Generator;
use Homie\Expression\Action;
use Homie\TodoList\VO\TodoItemVO;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("TodoList.ExpressionLanguage")
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{

    /**
     * @var TodoReminder
     */
    private $todoReminder;

    /**
     * @var TodoList
     */
    private $todoList;

    /**
     * @Inject({
     *     "@TodoList.TodoReminder",
     *     "@TodoList"
     * })
     * @param TodoReminder $todoReminder
     * @param TodoList $todoList
     */
    public function __construct(TodoReminder $todoReminder, TodoList $todoList)
    {
        $this->todoReminder = $todoReminder;
        $this->todoList     = $todoList;
    }

    /**
     * @return Generator
     */
    public function getFunctions()
    {
        yield new Action('sayTodoList', function () {
            $this->todoReminder->sendNotification();
        });

        yield new Action('addTodoTodoItem', function (array $parameters, string $name) {
            unset($parameters);

            $user = new AnonymusUserVO();
            $item = new TodoItemVO();
            $item->name = $name;

            $this->todoList->addItem($user, $item);
        });
    }
}
