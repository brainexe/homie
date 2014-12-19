<?php

namespace Tests\Raspberry\Gpio\GpioManager;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Gpio\GpioManager;
use Raspberry\Gpio\Pin;
use Raspberry\Gpio\PinGateway;
use Raspberry\Client\LocalClient;
use Raspberry\Gpio\PinLoader;
use Raspberry\Gpio\PinsCollection;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

class GpioManagerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var GpioManager
	 */
	private $_subject;

	/**
	 * @var PinGateway|MockObject
	 */
	private $_mockPinGateway;

	/**
	 * @var LocalClient|MockObject
	 */
	private $_mockLocalClient;

	/**
	 * @var PinLoader|MockObject
	 */
	private $_mockPinLoader;

	public function setUp() {
		$this->_mockPinGateway = $this->getMock(PinGateway::class, [], [], '', false);
		$this->_mockLocalClient = $this->getMock(LocalClient::class, [], [], '', false);
		$this->_mockPinLoader = $this->getMock(PinLoader::class, [], [], '', false);

		$this->_subject = new GpioManager($this->_mockPinGateway, $this->_mockLocalClient, $this->_mockPinLoader);
	}

	public function testGetPins() {
		$pin_id = 12;

		$descriptions = [
			$pin_id => $description = 'description'
		];

		$pin = new Pin();
		$pin->setID($pin_id);

		$pin_collection = new PinsCollection();
		$pin_collection->add($pin);

		$this->_mockPinLoader
			->expects($this->once())
			->method('loadPins')
			->will($this->returnValue($pin_collection));

		$this->_mockPinGateway
			->expects($this->once())
			->method('getPinDescriptions')
			->will($this->returnValue($descriptions));

		$actual_result = $this->_subject->getPins();

		$this->assertEquals($pin_collection, $actual_result);
		$this->assertEquals($description, $pin->getDescription());
	}

	public function testSetPin() {
		$id     = 10;
		$status = true;
		$value  = true;

		$pin = new Pin();
		$pin->setID($id);

		$this->_mockPinLoader
			->expects($this->once())
			->method('loadPin')
			->with($id)
			->will($this->returnValue($pin));

		$this->_mockLocalClient
			->expects($this->at(0))
			->method('execute')
			->with(sprintf(GpioManager::GPIO_COMMAND_DIRECTION, $id, 'out'));

		$this->_mockLocalClient
			->expects($this->at(1))
			->method('execute')
			->with(sprintf(GpioManager::GPIO_COMMAND_VALUE, $id, 1));

		$actual_result = $this->_subject->setPin($id, $status, $value);

		$this->assertEquals($pin, $actual_result);
	}

}
