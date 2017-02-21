<?php

namespace Homie\ShoppingList;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation
 */
class Controller
{

    /**
     * @var ShoppingList
     */
    private $shoppingList;

    /**
     * @param ShoppingList $list
     */
    public function __construct(
        ShoppingList $list
    ) {
        $this->shoppingList = $list;
    }

    /**
     * @Route("/shopping/", name="todo.shopping.index", methods="GET")
     * @return array
     */
    public function index() : array
    {
        $shoppingList = $this->shoppingList->getItems();

        return [
            'shoppingList' => $shoppingList,
        ];
    }

    /**
     * @param Request $request
     * @Route("/shopping/", name="todo.shopping.add", methods="POST")
     * @return bool
     */
    public function addItem(Request $request) : bool
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
    public function removeItem(Request $request, string $name) : bool
    {
        unset($request);
        $this->shoppingList->removeItem(urldecode($name));

        return true;
    }
}
