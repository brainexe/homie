<?php

namespace Homie\ShoppingList;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;

/**
 * @Service("ShoppingList", public=false)
 */
class ShoppingList
{

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @Inject("@ShoppingList.Gateway")
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
    public function addItem($name)
    {
        $this->gateway->addItem($name);
    }

    /**
     * @param string $name
     */
    public function removeItem($name)
    {
        $this->gateway->removeItem($name);
    }
}