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
		$descriptions = ['descriptions'];

		$this->_mockRedis
			->expects($this->once())
			->method('hGetAll')
			->with(PinGateway::REDIS_PINS)
			->will($this->returnValue($descriptions));

		$actual_result = $this->_subject->getPinDescriptions();

		$this->assertEquals($descriptions, $actual_result);
	}

}