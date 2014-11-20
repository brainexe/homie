<?php

namespace Tests\Raspberry\Gpio\PinLoader;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Gpio\GpioManager;
use Raspberry\Gpio\Pin;
use Raspberry\Gpio\PinLoader;
use Raspberry\Client\LocalClient;
use Raspberry\Gpio\PinsCollection;

/**
 * @Covers Raspberry\Gpio\PinLoader
 */
class PinLoaderTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var PinLoader
	 */
	private $_subject;

	/**
	 * @var LocalClient|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockLocalClient;

	public function setUp() {
		$this->_mockLocalClient = $this->getMock(LocalClient::class, [], [], '', false);

		$this->_subject = new PinLoader($this->_mockLocalClient);
	}

	public function testGetPins() {
		$id = 12;
		$name = 'name';
		$direction = 'IN';
		$value = 'Low';
		$description = 'descriptions';

		$gpio_result = "+----------+-Rev2-+------+--------+------+-------+
| wiringPi | GPIO | Phys | Name   | Mode | Value |
+----------+------+------+--------+------+-------+
|      $id   |  17  |  11  | $name | $direction   | $value   |
+----------+------+------+--------+------+-------+\n";

		$this->_mockLocalClient
			->expects($this->once())
			->method('executeWithReturn')
			->with(GpioManager::GPIO_COMMAND_READALL)
			->will($this->returnValue($gpio_result));

		$actual_result = $this->_subject->loadPins();

		$expected_pin = new Pin();
		$expected_pin->setID($id);
		$expected_pin->setName($name);
		$expected_pin->setDirection($direction);
		$expected_pin->setValue(0);

		$expected_pin_collection = new PinsCollection();
		$expected_pin_collection->add($expected_pin);

		$this->assertEquals($expected_pin_collection, $actual_result);
		$this->assertEquals($direction, $expected_pin->getDirection());
		$this->assertEquals(0, $expected_pin->getValue());

		$actual_result = $this->_subject->loadPins();
		$this->assertEquals($expected_pin_collection, $actual_result);

		$this->assertEquals($expected_pin, $this->_subject->loadPin($id));
	}
}
