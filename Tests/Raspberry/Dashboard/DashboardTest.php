<?php

namespace Tests\Raspberry\Dashboard\Dashboard;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Dashboard\AbstractWidget;
use Raspberry\Dashboard\Dashboard;
use Raspberry\Dashboard\WidgetFactory;
use Raspberry\Dashboard\Widgets\TimeWidget;
use Redis;
use BrainExe\Core\Util\IdGenerator;

/**
 * @Covers Raspberry\Dashboard\Dashboard
 */
class DashboardTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Dashboard
	 */
	private $_subject;

	/**
	 * @var WidgetFactory|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockWidgetFactory;

	/**
	 * @var Redis|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedis;

	/**
	 * @var IdGenerator|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockIdGenerator;

	public function setUp() {

		$this->_mockWidgetFactory = $this->getMock(WidgetFactory::class, [], [], '', false);
		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->_mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);
		$this->_subject = new Dashboard($this->_mockWidgetFactory);
		$this->_subject->setRedis($this->_mockRedis);
		$this->_subject->setIdGenerator($this->_mockIdGenerator);
	}

	public function testGetDashboard() {
		$user_id = 42;

		$payload = ['payload'];
		$widgets_raw = [
			$widget_id = 10 => json_encode($payload)
		];

		$this->_mockRedis
			->expects($this->once())
			->method('hGetAll')
			->with("dashboard:$user_id")
			->will($this->returnValue($widgets_raw));

		$actual_result = $this->_subject->getDashboard($user_id);

		$expected_widget = $payload;
		$expected_widget['id'] = $widget_id;
		$expected_widget['open'] = true;


		$this->assertEquals([$expected_widget], $actual_result);
	}

	public function testGetAvailableWidgets() {
		$widgets = [];

		$this->_mockWidgetFactory
			->expects($this->once())
			->method('getAvailableWidgets')
			->will($this->returnValue($widgets));

		$actual_result = $this->_subject->getAvailableWidgets();

		$this->assertEquals($widgets, $actual_result);
	}

	public function testAddWidget() {
		$user_id = 42;
		$type = 'type';
		$payload = [];
		$payload['type'] = $type;

		$widget = $this->getMock(AbstractWidget::class);

		$this->_mockWidgetFactory
			->expects($this->once())
			->method('getWidget')
			->with($type)
			->will($this->returnValue($widget));

		$widget
			->expects($this->once())
			->method('validate')
			->with($payload);


		$new_id = 11880;
		$this->_mockIdGenerator
			->expects($this->once())
			->method('generateRandomNumericId')
			->will($this->returnValue($new_id));

		$this->_mockRedis
			->expects($this->once())
			->method('HSET')
			->with("dashboard:$user_id", $new_id, json_encode($payload));

		$this->_subject->addWidget($user_id, $type, $payload);
	}

	public function testDeleteWidget() {
		$widget_id = 1;
		$user_id = 42;

		$this->_mockRedis
			->expects($this->once())
			->method('HDEL')
			->with("dashboard:$user_id", $widget_id);

		$this->_subject->deleteWidget($user_id, $widget_id);
	}

}
