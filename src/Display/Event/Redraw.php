<?php

namespace Homie\Display\Event;

use BrainExe\Core\EventDispatcher\AbstractEvent;

class Redraw extends AbstractEvent
{

    const KEY = 'display.redraw';

    /**
     * @var int
     */
    private $displayId;

    /**
     * @param int $displayId
     */
    public function __construct(int $displayId)
    {
        parent::__construct(self::KEY);

        $this->displayId = $displayId;
    }

    /**
     * @return int
     */
    public function getDisplayId() : int
    {
        return $this->displayId;
    }
}
