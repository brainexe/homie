<?php

namespace Raspberry\Sensors;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocketInterface;
use Raspberry\Sensors\Sensors\SensorInterface;

class SensorValueEvent extends AbstractEvent implements PushViaWebsocketInterface
{

    const VALUE = 'sensor.value';

    /**
     * @var SensorVO
     */
    public $sensorVo;

    /**
     * @var SensorInterface
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
     * @param SensorInterface $sensor
     * @param float $value
     * @param string $valueFormatted
     * @param integer $timestamp
     */
    public function __construct(
        SensorVO $sensorVo,
        SensorInterface $sensor,
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
