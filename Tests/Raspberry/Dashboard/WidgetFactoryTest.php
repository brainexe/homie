<?php

namespace Tests\Raspberry\Dashboard\Dashboard;

use PHPUnit_Framework_TestCase;

use Raspberry\Dashboard\WidgetFactory;
use Raspberry\Dashboard\WidgetInterface;
use Raspberry\Dashboard\Widgets\TimeWidget;

/**
 * @Covers Raspberry\Dashboard\Dashboard
 */
class WidgetFactoryTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var WidgetFactory
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new WidgetFactory();
		$this->_subject->addWidget('widget', new TimeWidget());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Invalid widget: invalid
	 */
	public function testGetInvalidWidget() {
		$this->_subject->getWidget('invalid');
	}

	public function testGetValidWidget() {
		$actual_result = $this->_subject->getWidget('widget');
		$this->assertTrue($actual_result instanceof WidgetInterface);
	}

	public function testGetWidgetTypes() {
		$actual_result = $this->_subject->getAvailableWidgets();
		$this->assertEquals(['widget'], $actual_result);
	}

}
