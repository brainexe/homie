<?php

namespace Tests\Raspberry\Gpio\GpioManager;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Gpio\GpioManager;
use Raspberry\Gpio\PinGateway;
use Raspberry\Client\LocalClient;

class GpioManagerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var GpioManager
	 */
	private $_subject;

	/**
	 * @var PinGateway|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockPinGateway;

	/**
	 * @var LocalClient|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockLocalClient;

	public function setUp() {
		$this->_mockPinGateway = $this->getMock(PinGateway::class, [], [], '', false);
		$this->_mockLocalClient = $this->getMock(LocalClient::class, [], [], '', false);
		$this->_subject = new GpioManager($this->_mockPinGateway, $this->_mockLocalClient);
	}

	public function testGetPins() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getPins();
	}

	public function testSetPin() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->setPin($id, $status, $value);
	}

}
