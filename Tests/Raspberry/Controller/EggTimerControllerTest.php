<?php

namespace Tests\Raspberry\Controller\EggTimerController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Controller\EggTimerController;
use Raspberry\EggTimer\EggTimer;

/**
 * @Covers Raspberry\Controller\EggTimerController
 */
class EggTimerControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var EggTimerController
	 */
	private $_subject;

	/**
	 * @var EggTimer|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEggTimer;


	public function setUp() {
		parent::setUp();

		$this->_mockEggTimer = $this->getMock(EggTimer::class, [], [], '', false);

		$this->_subject = new EggTimerController($this->_mockEggTimer);

	}

	public function testIndex() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->index();
	}

	public function testAdd() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->add($request);
	}

	public function testDeleteEggTimer() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->deleteEggTimer($request, $job_id);
	}

}
