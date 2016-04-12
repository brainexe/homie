<?php

namespace Homie\Sensors;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;

class SensorValueEvent extends AbstractEvent implements PushViaWebsocket
{

    const VALUE = 'sensor.value';
    const ERROR = 'sensor.value.error';

    /**
     * @var SensorVO
     */
    public $sensorVo;

    /**
     * @var float
     */
    public $value;

    /**
     * @var integer
     */
    public $timestamp;

    /**
     * @var string
     */
    public $valueFormatted;

    /**
     * @param string $eventName
     * @param SensorVO $sensorVo
     * @param float $value
     * @param string $valueFormatted
     * @param int $timestamp
     */
    public function __construct(
        $eventName,
        SensorVO $sensorVo,
        $value,
        $valueFormatted,
        $timestamp
    ) {
        parent::__construct($eventName);
        $this->sensorVo       = $sensorVo;
        $this->value          = $value;
        $this->valueFormatted = $valueFormatted;
        $this->timestamp      = $timestamp;
    }
}
