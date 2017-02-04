<?php

namespace Homie\TodoList\Controller;


use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\Exception\UserNotFoundException;
use BrainExe\Core\Authentication\LoadUser;
use BrainExe\Core\Translation\TranslationProvider;
use Generator;
use Homie\TodoList\TodoList;
use Homie\TodoList\VO\TodoItemVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller("TodoList.Controller.TodoListController")
 */
class TodoListController implements TranslationProvider
{

    const TOKEN_NAME = 'todo.status.%s.name';

    /**
     * @var TodoList
     */
    private $todo;

    /**
     * @var LoadUser
     */
    private $loadUser;

    /**
     * @param TodoList $todo
     * @param LoadUser $loadUser
     */
    public function __construct(
        TodoList $todo,
        LoadUser $loadUser
    ) {
        $this->todo     = $todo;
        $this->loadUser = $loadUser;
    }

    /**
     * @Route("/todo/", name="todo.index", methods="GET")
     * @return array
     */
    public function index() : array
    {
        $list = $this->todo->getList();

        return [
            'list'   => iterator_to_array($list),
            'states' => self::getStates()
        ];
    }

    /**
     * @param Request $request
     * @Route("/todo/", name="todo.add", methods="POST")
     * @return TodoItemVO
     */
    public function addItem(Request $request) : TodoItemVO
    {
        $itemVo                 = new TodoItemVO();
        $itemVo->name           = $request->request->get('name');
        $itemVo->description    = $request->request->get('description');
        $itemVo->status         = $request->request->get('status');
        $itemVo->deadline       = strtotime($request->request->get('deadline')) ?? null;
        $itemVo->cronExpression = $request->request->get('cronExpression');

        $user = $request->attributes->get('user');

        $this->todo->addItem($user, $itemVo);

        return $itemVo;
    }

    /**
     * @param Request $request
     * @Route("/todo/", name="todo.edit", methods="PUT")
     * @return TodoItemVO
     */
    public function editItem(Request $request) : TodoItemVO
    {
        $itemId  = $request->request->getInt('id');
        $changes = (array)$request->request->get('changes');

        return $this->todo->editItem($itemId, $changes);
    }

    /**
     * @param Request $request
     * @Route("/todo/assign/", name="todo.assign", methods="POST")
     * @return TodoItemVO
     * @throws UserNotFoundException
     */
    public function setAssignee(Request $request) : TodoItemVO
    {
        $itemId = $request->request->getInt('todoId');
        $userId = $request->request->getInt('userId');

        $user = $this->loadUser->loadUserById($userId);

        return $this->todo->editItem($itemId, [
            'userId'   => $userId,
            'userName' => $user->username,
        ]);
    }

    /**
     * @param Request $request
     * @param int $itemId
     * @Route("/todo/{itemId}/", name="todo.delete", requirements={"itemId":"\d+"})
     * @return bool
     */
    public function deleteItem(Request $request, int $itemId) : bool
    {
        unset($request);

        $this->todo->deleteItem($itemId);

        return true;
    }

    /**
     * @return array[]
     */
    private static function getStates() : array
    {
        return [
            TodoItemVO::STATUS_PENDING => [
                'next' => [TodoItemVO::STATUS_OPEN, 'delete']
            ],
            TodoItemVO::STATUS_OPEN => [
                'next' => [TodoItemVO::STATUS_PROGRESS, TodoItemVO::STATUS_COMPLETED, 'delete']
            ],
            TodoItemVO::STATUS_PROGRESS => [
                'next' => [TodoItemVO::STATUS_COMPLETED, TodoItemVO::STATUS_OPEN, 'delete']
            ],
            TodoItemVO::STATUS_COMPLETED => [
                'next' => [TodoItemVO::STATUS_PROGRESS, TodoItemVO::STATUS_OPEN, 'delete'],
                'hidden' => true
            ],
        ];
    }

    /**
     * @return Generator|string[]
     */
    public static function getTokens()
    {
        $states = array_keys(self::getStates());
        $states[] = 'delete';

        foreach ($states as $stateId) {
            yield sprintf(self::TOKEN_NAME, $stateId);
        }
    }
}
