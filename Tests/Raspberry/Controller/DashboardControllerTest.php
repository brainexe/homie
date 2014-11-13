<?php

namespace Tests\Raspberry\Controller\DashboardController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Controller\DashboardController;
use Symfony\Component\HttpFoundation\Request;
use Raspberry\Dashboard\Dashboard;

/**
 * @Covers Raspberry\Controller\DashboardController
 */
class DashboardControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var DashboardController
	 */
	private $_subject;

	/**
	 * @var Dashboard|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockDashboard;

	public function setUp() {
		$this->_mockDashboard = $this->getMock(Dashboard::class, [], [], '', false);
		$this->_subject = new DashboardController($this->_mockDashboard);

	}

	public function testIndex() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$actual_result = $this->_subject->index($request);
	}

	public function testAddWidget() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$actual_result = $this->_subject->addWidget($request);
	}

	public function testDeleteWidget() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$actual_result = $this->_subject->deleteWidget($request);
	}

}
