<?php

namespace Homie\Sensors;

class SensorVO
{

    /**
     * @var int
     */
    public $sensorId;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $parameter;

    /**
     * @var int
     */
    public $interval;

    /**
     * @var int
     */
    public $node;

    /**
     * @var string
     */
    public $formatter;

    /**
     * @var int - unix timestamp
     */
    public $lastValueTimestamp;

    /**
     * @var float
     */
    public $lastValue;

    /**
     * @var string (#hex color)
     */
    public $color;

    /**
     * @var string[]
     */
    public $tags = [];
}
