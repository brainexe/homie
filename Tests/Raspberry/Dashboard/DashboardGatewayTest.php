<?php

namespace Tests\Raspberry\Dashboard;

use BrainExe\Core\Redis\Redis;
use BrainExe\Core\Redis\RedisLogger;
use BrainExe\Core\Util\IdGenerator;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Dashboard\DashboardGateway;

/**
 * @Covers Raspberry\Dashboard\DashboardGateway
 */
class DashboardGatewayTest extends TestCase {

	/**
	 * @var DashboardGateway
	 */
	private $subject;

	/**
	 * @var Redis|MockObject
	 */
	private $mockRedis;

	/**
	 * @var IdGenerator|MockObject
	 */
	private $mockIdGenerator;

	public function setUp() {
		$this->mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->mockIdGenerator = $this->getMock(IdGenerator::class);

		$this->subject = new DashboardGateway();
		$this->subject->setRedis($this->mockRedis);
		$this->subject->setIdGenerator($this->mockIdGenerator);
	}

	public function testGetDashboard() {
		$user_id = 42;

		$payload = ['payload'];
		$widgets_raw = [
			$widget_id = 10 => json_encode($payload)
		];

		$this->mockRedis
			->expects($this->once())
			->method('hGetAll')
			->with("dashboard:$user_id")
			->will($this->returnValue($widgets_raw));

		$actual_result = $this->subject->getDashboard($user_id);

		$expected_widget = $payload;
		$expected_widget['id'] = $widget_id;
		$expected_widget['open'] = true;

		$this->assertEquals([$expected_widget], $actual_result);
	}

	public function testAddWidget() {
		$user_id         = 42;
		$type            = 'type';
		$payload         = [];
		$payload['type'] = $type;

		$new_id = 11880;
		$this->mockIdGenerator
			->expects($this->once())
			->method('generateRandomNumericId')
			->will($this->returnValue($new_id));

		$this->mockRedis
			->expects($this->once())
			->method('HSET')
			->with("dashboard:$user_id", $new_id, json_encode($payload));

		$this->subject->addWidget($user_id, $payload);
	}

	public function testDeleteWidget() {
		$widget_id = 1;
		$user_id = 42;

		$this->mockRedis
			->expects($this->once())
			->method('HDEL')
			->with("dashboard:$user_id", $widget_id);

		$this->subject->deleteWidget($user_id, $widget_id);
	}

}
