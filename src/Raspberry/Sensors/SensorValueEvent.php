<?php

namespace Raspberry\Sensors;


use Matze\Core\EventDispatcher\AbstractEvent;
use Raspberry\Sensors\Sensors\SensorInterface;

class SensorValueEvent extends AbstractEvent {

    const VALUE = 'sensor.value';

    /**
     * @var SensorVO
     */
    public $sensor_vo;

    /**
     * @var SensorInterface
     */
    public $sensor;

    /**
     * @var float
     */
    public $value;

    /**
     * @param SensorVO $sensor_vo
     * @param SensorInterface $sensor
     * @param float $value
     */
    function __construct(SensorVO $sensor_vo, SensorInterface $sensor, $value) {
        $this->event_name = self::VALUE;
        $this->sensor_vo = $sensor_vo;
        $this->value = $value;
        $this->sensor = $sensor;
    }
}