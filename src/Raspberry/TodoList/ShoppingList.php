<?php

namespace Raspberry\TodoList;

/**
 * @Service(public=false)
 */
class ShoppingList
{

    /**
     * @var ShoppingListGateway
     */
    private $gateway;

    /**
     * @Inject("@ShoppingListGateway")
     * @param ShoppingListGateway $gateway
     */
    public function __construct(ShoppingListGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return string[]
     */
    public function getShoppingListItems()
    {
        return $this->gateway->getShoppingListItems();
    }

    /**
     * @param string $name
     */
    public function addShoppingListItem($name)
    {
        $this->gateway->addShoppingListItem($name);
    }

    /**
     * @param string $name
     */
    public function removeShoppingListItem($name)
    {
        $this->gateway->removeShoppingListItem($name);
    }
}
