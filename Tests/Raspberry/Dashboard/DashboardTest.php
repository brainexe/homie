<?php

namespace Tests\Raspberry\Dashboard\Dashboard;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Dashboard\AbstractWidget;
use Raspberry\Dashboard\Dashboard;
use Raspberry\Dashboard\DashboardGateway;
use Raspberry\Dashboard\WidgetFactory;

/**
 * @Covers Raspberry\Dashboard\Dashboard
 */
class DashboardTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Dashboard
	 */
	private $subject;

	/**
	 * @var WidgetFactory|MockObject
	 */
	private $mockWidgetFactory;

	/**
	 * @var DashboardGateway|MockObject
	 */
	private $mockGateway;

	public function setUp() {
		$this->mockWidgetFactory = $this->getMock(WidgetFactory::class, [], [], '', false);
		$this->mockGateway       = $this->getMock(DashboardGateway::class, [], [], '', false);

		$this->subject = new Dashboard($this->mockGateway, $this->mockWidgetFactory);
	}

	public function testGetDashboard() {
		$dashboard = [];
		$userId    = 10;

		$this->mockGateway
			->expects($this->once())
			->method('getDashboard')
			->willReturn($dashboard);

		$actualResult = $this->subject->getDashboard($userId);

		$this->assertEquals($dashboard, $actualResult);
	}

	public function testGetAvailableWidgets() {
		$widgets = [];

		$this->mockWidgetFactory
			->expects($this->once())
			->method('getAvailableWidgets')
			->will($this->returnValue($widgets));

		$actual_result = $this->subject->getAvailableWidgets();

		$this->assertEquals($widgets, $actual_result);
	}

	public function testAddWidget() {
		$user_id = 42;
		$type = 'type';
		$payload = [];
		$payload['type'] = $type;

		$widget = $this->getMock(AbstractWidget::class);

		$this->mockWidgetFactory
			->expects($this->once())
			->method('getWidget')
			->with($type)
			->willReturn($widget);

		$widget
			->expects($this->once())
			->method('validate')
			->with($payload);

		$this->mockGateway
			->expects($this->once())
			->method('addWidget')
			->with($user_id, $payload);

		$this->subject->addWidget($user_id, $type, $payload);
	}

	public function testDeleteWidget() {
		$widget_id = 1;
		$user_id   = 42;

		$this->mockGateway
			->expects($this->once())
			->method('deleteWidget')
			->with($user_id, $widget_id);

		$this->subject->deleteWidget($user_id, $widget_id);
	}

}
