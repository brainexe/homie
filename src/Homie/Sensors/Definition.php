<?php

namespace Homie\Sensors;

use Homie\Sensors\Formatter\None;

class Definition
{

    const TYPE_TEMPERATURE = 'temperature';
    const TYPE_BAROMETER   = 'barometer';
    const TYPE_HUMIDITY    = 'humidity';
    const TYPE_DISK        = 'disk';
    const TYPE_LOAD        = 'load';
    const TYPE_MEMORY      = 'memory';
    const TYPE_NONE        = 'none';

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $formatter = None::TYPE;

    /**
     * @var string
     */
    public $unit;
}
