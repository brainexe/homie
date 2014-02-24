<?php

namespace Raspberry\Dashboard\Widgets;

use Raspberry\Dashboard\AbstractWidget;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\Sensors\SensorInterface;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class SensorWidget extends AbstractWidget {

	const TYPE = 'sensor';

	/**
	 * @var SensorInterface
	 */
	private $_sensor;

	/**
	 * @var array
	 */
	private $_sensor_data;

	/**
	 * @var SensorGateway
	 */
	private $_sensor_gateway;

	/**
	 * @var SensorBuilder
	 */
	private $_sensor_builder;

	/**
	 * @Inject({"@SensorGateway", "@SensorBuilder"})
	 */
	public function __construct(SensorGateway $sensor_gateway, SensorBuilder $sensor_builder) {
		$this->_sensor_gateway = $sensor_gateway;
		$this->_sensor_builder = $sensor_builder;
	}

	/**
	 * @return string
	 */
	public function renderWidget() {
		return $this->render('widgets/sensor_widget.html.twig', [
			'title' => $this->_sensor_data['name'],
			'temperature' => $this->_sensor->formatValue($this->_sensor_data['last_value']),
			'sensor' => $this->_sensor_data
		]);
	}

	/**
	 * @return string
	 */
	public function getId() {
		return self::TYPE;
	}

	/**
	 * @param array $payload
	 * @throws \InvalidArgumentException
	 */
	public function create(array $payload) {
		if (empty($payload['sensor_id'])) {
			throw new \InvalidArgumentException("No sensor_id passed");
		}
		$this->_sensor_data = $this->_sensor_gateway->getSensor($payload['sensor_id']);
		$this->_sensor = $this->_sensor_builder->build($this->_sensor_data['type']);
	}
}
