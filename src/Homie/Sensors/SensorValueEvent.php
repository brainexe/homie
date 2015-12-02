<?php

namespace Homie\Sensors;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;
use Homie\Sensors\Interfaces\Sensor;

class SensorValueEvent extends AbstractEvent
{

    const VALUE = 'sensor.value';
    const ERROR = 'sensor.value.error';

    /**
     * @var SensorVO
     */
    public $sensorVo;

    /**
     * @var Sensor
     */
    public $sensor;

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
     * @param Sensor $sensor
     * @param float $value
     * @param string $valueFormatted
     * @param integer $timestamp
     */
    public function __construct(
        $eventName,
        SensorVO $sensorVo,
        Sensor $sensor,
        $value,
        $valueFormatted,
        $timestamp
    ) {
        parent::__construct($eventName);
        $this->sensorVo       = $sensorVo;
        $this->value          = $value;
        $this->sensor         = $sensor;
        $this->valueFormatted = $valueFormatted;
        $this->timestamp      = $timestamp;
    }
}
