<?php

namespace Tests\Raspberry\Dashboard;

use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\IdGenerator;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Dashboard\DashboardGateway;

/**
 * @Covers Raspberry\Dashboard\DashboardGateway
 */
class DashboardGatewayTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var DashboardGateway
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $mockRedis;

    /**
     * @var IdGenerator|MockObject
     */
    private $mockIdGenerator;

    public function setUp()
    {
        $this->mockRedis = $this->getRedisMock();
        $this->mockIdGenerator = $this->getMock(IdGenerator::class);

        $this->subject = new DashboardGateway();
        $this->subject->setRedis($this->mockRedis);
        $this->subject->setIdGenerator($this->mockIdGenerator);
    }

    public function testGetDashboard()
    {
        $userId = 42;

        $payload = ['payload'];
        $widgetsRaw = [
            $widgetId = 10 => json_encode($payload)
        ];

        $this->mockRedis
            ->expects($this->once())
            ->method('hGetAll')
            ->with("dashboard:$userId")
            ->willReturn($widgetsRaw);

        $actualResult = $this->subject->getDashboard($userId);

        $expectedWidget = $payload;
        $expectedWidget['id'] = $widgetId;
        $expectedWidget['open'] = true;

        $this->assertEquals([$expectedWidget], $actualResult);
    }

    public function testAddWidget()
    {
        $userId             = 42;
        $type            = 'type';
        $payload         = [];
        $payload['type'] = $type;

        $newId = 11880;
        $this->mockIdGenerator
            ->expects($this->once())
            ->method('generateRandomNumericId')
            ->willReturn($newId);

        $this->mockRedis
            ->expects($this->once())
            ->method('HSET')
            ->with("dashboard:$userId", $newId, json_encode($payload));

        $this->subject->addWidget($userId, $payload);
    }

    public function testDeleteWidget()
    {
        $widgetId = 1;
        $userId   = 42;

        $this->mockRedis
            ->expects($this->once())
            ->method('HDEL')
            ->with("dashboard:$userId", $widgetId);

        $this->subject->deleteWidget($userId, $widgetId);
    }
}
