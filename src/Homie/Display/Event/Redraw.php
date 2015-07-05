<?php

namespace Homie\Display\Event;

use BrainExe\Core\EventDispatcher\AbstractEvent;

class Redraw extends AbstractEvent
{

    const KEY = 'display:redraw';

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(self::KEY);
    }
}
