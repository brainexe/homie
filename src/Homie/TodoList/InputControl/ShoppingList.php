<?php

namespace Homie\TodoList\InputControl;

use BrainExe\InputControl\Annotations\InputControlInterface;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;
use BrainExe\Annotations\Annotations\Inject;
use BrainExe\InputControl\Event;
use Homie\TodoList\ShoppingList as ShoppingListModel;

/**
 * @InputControlAnnotation(name="TodoList.ShoppingList")
 */
class ShoppingList implements InputControlInterface
{

    /**
     * @var ShoppingListModel
     */
    private $shoppingList;

    /**
     * @Inject("@ShoppingList")
     * @param ShoppingListModel $shoppingList
     */
    public function __construct(ShoppingListModel $shoppingList)
    {
        $this->shoppingList = $shoppingList;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            '/^add shopping item (.*)/i' => 'add',
            '/^delete shopping item (.*)/i' => 'delete',
        ];
    }

    /**
     * @param Event $event
     */
    public function add(Event $event)
    {
        $this->shoppingList->addShoppingListItem($event->match);
    }

    /**
     * @param Event $event
     */
    public function delete(Event $event)
    {
        $this->shoppingList->removeShoppingListItem($event->match);
    }
}
