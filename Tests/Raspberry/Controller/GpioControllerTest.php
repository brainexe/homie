<?php

namespace Tests\Raspberry\Controller\GpioController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Controller\GpioController;
use Symfony\Component\HttpFoundation\Request;
use Raspberry\Gpio\GpioManager;

/**
 * @Covers Raspberry\Controller\GpioController
 */
class GpioControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var GpioController
	 */
	private $_subject;

	/**
	 * @var GpioManager|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockGpioManager;

	public function setUp() {

		$this->_mockGpioManager = $this->getMock(GpioManager::class, [], [], '', false);
		$this->_subject = new GpioController($this->_mockGpioManager);

	}

	public function testIndex() {
		$this->markTestIncomplete('This is only a dummy implementation');


		$actual_result = $this->_subject->index();
	}

	public function testSetStatus() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$id = null;
		$status = null;
		$value = null;
		$actual_result = $this->_subject->setStatus($request, $id, $status, $value);
	}

}
