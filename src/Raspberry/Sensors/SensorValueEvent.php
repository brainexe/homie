<?php

namespace Raspberry\Sensors;

use Matze\Core\EventDispatcher\AbstractEvent;
use Matze\Core\EventDispatcher\PushViaWebsocketInterface;
use Raspberry\Sensors\Sensors\SensorInterface;

class SensorValueEvent extends AbstractEvent implements PushViaWebsocketInterface {

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
	 * @var integer
	 */
	public $timestamp;

	/**
	 * @var string
	 */
	public $value_formatted;

	/**
	 * @param SensorVO $sensor_vo
	 * @param SensorInterface $sensor
	 * @param float $value
	 * @param string $value_formatted
	 * @param integer $timestamp
	 */
    function __construct(SensorVO $sensor_vo, SensorInterface $sensor, $value, $value_formatted, $timestamp) {
        $this->event_name = self::VALUE;
        $this->sensor_vo = $sensor_vo;
        $this->value = $value;
        $this->sensor = $sensor;
		$this->value_formatted = $value_formatted;
		$this->timestamp = $timestamp;
	}
}