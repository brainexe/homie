<?php

namespace Tests\Raspberry\Dashboard\Widgets\TimeWidget;

use PHPUnit_Framework_TestCase;

use Raspberry\Dashboard\Widgets\TimeWidget;

class TimeWidgetTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TimeWidget
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new TimeWidget();
	}

	public function testGetId() {
		$actual_result = $this->_subject->getId();
		$this->assertEquals(TimeWidget::TYPE, $actual_result);
	}

	public function testValidate() {
		$payload = [];

		$actual_result = $this->_subject->validate($payload);
		$this->assertTrue($actual_result);
	}

	public function testCreate() {
		$payload = [];

		$this->_subject->create($payload);
	}

}
