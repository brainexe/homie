<?php

namespace Tests\Raspberry\Dashboard\Widgets\SensorWidget;

use PHPUnit_Framework_TestCase;
use Raspberry\Dashboard\Widgets\SensorWidget;

/**
 * @Covers Raspberry\Dashboard\Widgets\SensorWidget
 */
class SensorWidgetTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SensorWidget
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new SensorWidget();
	}

	public function testGetId() {
		$actual_result = $this->_subject->getId();
		$this->assertEquals(SensorWidget::TYPE, $actual_result);
	}

	/**
	 * @expectedException \BrainExe\Core\Application\UserException
	 * @expectedExceptionMessage No sensor_id passe
	 */
	public function testCreateWithoutSensorId() {
		$payload = [];

		$this->_subject->create($payload);
	}

	public function testCreate() {
		$payload = [
			'sensor_id' => 1
		];

		$this->_subject->create($payload);
	}

}
