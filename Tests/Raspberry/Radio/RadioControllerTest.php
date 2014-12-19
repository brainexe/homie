<?php

namespace Tests\Raspberry\Radio\RadioController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Radio\RadioController;
use Raspberry\Client\LocalClient;

/**
 * @Covers Raspberry\Radio\RadioController
 */
class RadioControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RadioController
	 */
	private $_subject;

	/**
	 * @var LocalClient|MockObject
	 */
	private $_mockLocalClient;

	public function setUp() {
		$this->_mockLocalClient = $this->getMock(LocalClient::class, [], [], '', false);
		$this->_subject = new RadioController($this->_mockLocalClient);
	}

	public function testSetStatus() {
		$code = 0;
		$number = 1;
		$status = 1;

		$this->_mockLocalClient
			->expects($this->once())
			->method('execute')
			->with($this->anything());

		$this->_subject->setStatus($code, $number, $status);
	}

}
