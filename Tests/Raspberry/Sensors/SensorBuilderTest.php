<?php

namespace Raspberry\Tests\Sensors;

use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\Sensors\SensorInterface;

class SensorBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var SensorBuilder
	 */
	private $_subject;


	public function setUp() {
		$this->_subject = new SensorBuilder();
	}

	public function testGetSensors() {
		/** @var SensorInterface|PHPUnit_Framework_MockObject_MockObject $sensor_mock */
		$sensor_mock = $this->getMock(SensorInterface::class);
		$sensor_type = 'sensor_123';

		$this->_subject->addSensor($sensor_type, $sensor_mock);
		$actual_result = $this->_subject->getSensors();

		$this->assertEquals([$sensor_type => $sensor_mock], $actual_result);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Invalid sensor type: sensor_123
	 */
	public function testBuildInvalid() {
		$sensor_type = 'sensor_123';

		$this->_subject->build($sensor_type);
	}

	public function testBuildValid() {
		/** @var SensorInterface|PHPUnit_Framework_MockObject_MockObject $sensor_mock */
		$sensor_mock = $this->getMock('Raspberry\Sensors\Sensors\SensorInterface');
		$sensor_type = 'sensor_123';

		$this->_subject->addSensor($sensor_type, $sensor_mock);

		$actual_result = $this->_subject->build($sensor_type);

		$this->assertEquals($sensor_mock, $actual_result);
	}
}
