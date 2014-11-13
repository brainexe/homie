<?php

namespace Tests\Raspberry\Gpio\PinGateway;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Gpio\PinGateway;
use Redis;

/**
 * @Covers Raspberry\Gpio\PinGateway
 */
class PinGatewayTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var PinGateway
	 */
	private $_subject;

	/**
	 * @var Redis|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedis;

	public function setUp() {

		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->_subject = new PinGateway();
		$this->_subject->setRedis($this->_mockRedis);
	}

	public function testGetPinDescriptions() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getPinDescriptions();
	}

}
