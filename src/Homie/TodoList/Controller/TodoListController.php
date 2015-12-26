<?php

namespace Homie\TodoList\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\LoadUser;
use Homie\TodoList\TodoList;
use Homie\TodoList\VO\TodoItemVO;

use Symfony\Component\HttpFoundation\Request;

/**
 * @todo repeatable tasks
 * @Controller("ToDoListController")
 */
class TodoListController
{

    /**
     * @var TodoList
     */
    private $todo;

    /**
     * @var LoadUser
     */
    private $loadUser;

    /**
     * @Inject({"@TodoList", "@Authentication.LoadUser"})
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
    public function index()
    {
        $list = $this->todo->getList();

        return [
            'list'   => iterator_to_array($list),
            'states' => $this->getStates()
        ];
    }

    /**
     * @param Request $request
     * @Route("/todo/", name="todo.add", methods="POST")
     * @return TodoItemVO
     */
    public function addItem(Request $request)
    {
        $itemVo                 = new TodoItemVO();
        $itemVo->name           = $request->request->get('name');
        $itemVo->description    = $request->request->get('description');
        $itemVo->status         = $request->request->get('status');
        $itemVo->deadline       = strtotime($request->request->get('deadline'));
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
    public function setItemStatus(Request $request)
    {
        $itemId  = $request->request->getInt('id');
        $changes = $request->request->get('changes');

        return $this->todo->editItem($itemId, $changes);
    }

    /**
     * @param Request $request
     * @Route("/todo/assign/", name="todo.assign", methods="POST")
     * @return TodoItemVO
     */
    public function setAssignee(Request $request)
    {
        $itemId = $request->request->getInt('id');
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
     * @Route("/todo/{itemId}/", name="todo.delete")
     * @return bool
     */
    public function deleteItem(Request $request, $itemId)
    {
        unset($request);

        $this->todo->deleteItem($itemId);

        return true;
    }

    /**
     * @return array[]
     */
    protected function getStates()
    {
        $states = [
            TodoItemVO::STATUS_PENDING => [
                'name' => _('Pending'),
                'next' => [TodoItemVO::STATUS_OPEN, 'delete']
            ],
            TodoItemVO::STATUS_OPEN => [
                'name' => _('Open'),
                'next' => [TodoItemVO::STATUS_PROGRESS, TodoItemVO::STATUS_COMPLETED, 'delete']
            ],
            TodoItemVO::STATUS_PROGRESS => [
                'name' => _('In Progress'),
                'next' => [TodoItemVO::STATUS_COMPLETED, 'delete']
            ],
            TodoItemVO::STATUS_COMPLETED => [
                'name' => _('Completed'),
                'next' => ['delete'],
                'hidden' => true
            ],
        ];

        return $states;
    }
}
