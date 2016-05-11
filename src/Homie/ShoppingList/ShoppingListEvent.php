<?php

namespace Homie\ShoppingList;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;
use BrainExe\Core\Traits\JsonSerializableTrait;

class ShoppingListEvent extends AbstractEvent implements PushViaWebsocket
{

    use JsonSerializableTrait;

    const REMOVE = 'shopping_list.remove';
    const ADD    = 'shopping_list.add';

    /**
     * @var string
     */
    private $item;

    public function __construct(string $eventName, string $item)
    {
        parent::__construct($eventName);
        $this->item = $item;
    }

    /**
     * @return string
     */
    public function getItem()
    {
        return $this->item;
    }
}
