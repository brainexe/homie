<?php

namespace Homie\TodoList\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Route;
use Homie\TodoList\ShoppingList;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller("ShoppingListController")
 */
class ShoppingListController
{

    /**
     * @var ShoppingList
     */
    private $shoppingList;

    /**
     * @Inject({"@ShoppingList"})
     * @param ShoppingList $list
     */
    public function __construct(
        ShoppingList $list
    ) {
        $this->shoppingList = $list;
    }

    /**
     * @Route("/shopping/", name="todo.shopping.index", methods="GET")
     * @return JsonResponse
     */
    public function index()
    {
        $shoppingList = $this->shoppingList->getItems();

        return new JsonResponse([
            'shoppingList' => $shoppingList,
        ]);
    }

    /**
     * @param Request $request
     * @Route("/shopping/", name="todo.shopping.add", methods="POST")
     * @return bool
     */
    public function addItem(Request $request)
    {
        $name = $request->request->get('name');

        $this->shoppingList->addItem($name);

        return true;
    }

    /**
     * @param Request $request
     * @param string $name
     * @return bool
     * @Route("/shopping/{name}/", name="todo.shopping.remove", methods="DELETE")
     */
    public function removeItem(Request $request, $name)
    {
        unset($request);
        $this->shoppingList->removeItem($name);

        return true;
    }
}
