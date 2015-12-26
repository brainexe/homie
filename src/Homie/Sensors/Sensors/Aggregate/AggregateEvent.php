<?php

namespace Homie\Sensors\Sensors\Aggregate;

use BrainExe\Core\EventDispatcher\AbstractEvent;

class AggregateEvent extends AbstractEvent
{
    const EVENT_NAME = 'sensor.aggregated';

    /**
     * @vars string
     */
    private $identifier;

    /**
     * @var float
     */
    private $value;

    /**
     * @param string $identifier
     * @param float $value
     */
    public function __construct($identifier, $value)
    {
        parent::__construct(self::EVENT_NAME);

        $this->identifier = $identifier;
        $this->value      = $value;
    }
}
