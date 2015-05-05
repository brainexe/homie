<?php

namespace Homie\Sensors;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;
use Homie\Sensors\Interfaces\Sensor;

class SensorValueEvent extends AbstractEvent implements PushViaWebsocket
{

    const VALUE = 'sensor.value';

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
     * @param SensorVO $sensorVo
     * @param Sensor $sensor
     * @param float $value
     * @param string $valueFormatted
     * @param integer $timestamp
     */
    public function __construct(
        SensorVO $sensorVo,
        Sensor $sensor,
        $value,
        $valueFormatted,
        $timestamp
    ) {
        $this->event_name = self::VALUE;
        $this->sensorVo = $sensorVo;
        $this->value = $value;
        $this->sensor = $sensor;
        $this->valueFormatted = $valueFormatted;
        $this->timestamp = $timestamp;
    }
}
