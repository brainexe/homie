<?php

namespace Homie\ShoppingList;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;

/**
 * @Service("ShoppingList")
 */
class ShoppingList
{

    use EventDispatcherTrait;

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return string[]
     */
    public function getItems() : array
    {
        return $this->gateway->getItems();
    }

    /**
     * @param string $name
     */
    public function addItem(string $name)
    {
        $this->gateway->addItem($name);

        $event = new ShoppingListEvent(ShoppingListEvent::ADD, $name);
        $this->dispatchEvent($event);
    }

    /**
     * @param string $name
     */
    public function removeItem(string $name)
    {
        $this->gateway->removeItem($name);

        $event = new ShoppingListEvent(ShoppingListEvent::REMOVE, $name);
        $this->dispatchEvent($event);
    }
}
