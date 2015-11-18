<?php

namespace Homie\Sensors\Sensors\Aggregate;

use BrainExe\Core\EventDispatcher\AbstractEvent;

class AggregateEvent extends AbstractEvent
{
    const EVENT_NAME = 'sensor.aggregated';

    /**
     * @param string $identifier
     * @param mixed $value
     */
    public function __construct($identifier, $value)
    {
        parent::__construct(self::EVENT_NAME);
    }
}
