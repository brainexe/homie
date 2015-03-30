<?php

namespace Tests\Raspberry\Dashboard;

use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\IdGenerator;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Dashboard\DashboardGateway;
use Raspberry\Dashboard\DashboardVo;

/**
 * @covers Raspberry\Dashboard\DashboardGateway
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
    private $redis;

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    public function setUp()
    {
        $this->redis       = $this->getRedisMock();
        $this->idGenerator = $this->getMock(IdGenerator::class);

        $this->subject = new DashboardGateway();
        $this->subject->setRedis($this->redis);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testGetDashboard()
    {
        $dashboardId = 42;

        $payload    = [
            'payload' => 'payload'
        ];

        $widgetsRaw = [
            10 => json_encode($payload)
        ];

        $meta = [
            'name' => 'mockName'
        ];

        $this->redis
            ->expects($this->at(0))
            ->method('hGetAll')
            ->with("dashboard:widgets:$dashboardId")
            ->willReturn($widgetsRaw);

        $this->redis
            ->expects($this->at(1))
            ->method('hGetAll')
            ->with("dashboard:meta:$dashboardId")
            ->willReturn($meta);

        $actualResult = $this->subject->getDashboard($dashboardId);

        $dashboard = new DashboardVo();

        $widget = [];
        $widget['open']    = true;
        $widget['payload'] = 'payload';

        $dashboard->widgets     = [$widget];
        $dashboard->dashboardId = $dashboardId;
        $dashboard->name        = 'mockName';

        $this->assertEquals($dashboard, $actualResult);
    }

    public function testGetDashboards()
    {
        $dashboardId = 42;

        $payload    = [
            'payload' => 'payload'
        ];

        $meta = [
            'name' => 'mockName'
        ];

        $widgetsRaw = [
            $widgetId = 10 => json_encode($payload)
        ];

        $this->redis
            ->expects($this->at(0))
            ->method('sMembers')
            ->with("dashboard:ids")
            ->willReturn([$dashboardId]);

        $this->redis
            ->expects($this->at(1))
            ->method('hGetAll')
            ->with("dashboard:widgets:$dashboardId")
            ->willReturn($widgetsRaw);

        $this->redis
            ->expects($this->at(2))
            ->method('hGetAll')
            ->with("dashboard:meta:$dashboardId")
            ->willReturn($meta);

        $actualResult = $this->subject->getDashboards();

        $dashboard = new DashboardVo();

        $widget = [];
        $widget['open']    = true;
        $widget['payload'] = 'payload';

        $dashboard->widgets = [$widget];
        $dashboard->dashboardId = $dashboardId;
        $dashboard->name = 'mockName';

        $this->assertEquals([$dashboardId => $dashboard], $actualResult);
    }

    public function testAddWidget()
    {
        $newId = 11880;

        $dashboardId     = 42;
        $type            = 'type';
        $payload         = [];
        $payload['type'] = $type;
        $payload['id']   = $newId;

        $this->idGenerator
            ->expects($this->once())
            ->method('generateRandomNumericId')
            ->willReturn($newId);

        $this->redis
            ->expects($this->once())
            ->method('hSet')
            ->with("dashboard:widgets:$dashboardId", $newId, json_encode($payload));

        $this->subject->addWidget($dashboardId, $payload);
    }

    public function testDeleteWidget()
    {
        $widgetId    = 1;
        $dashboardId = 42;

        $this->redis
            ->expects($this->once())
            ->method('HDEL')
            ->with("dashboard:widgets:$dashboardId", $widgetId);

        $this->subject->deleteWidget($dashboardId, $widgetId);
    }

    public function testDelete()
    {
        $dashboardId = 42;

        $this->redis
            ->expects($this->at(0))
            ->method('del')
            ->with("dashboard:widgets:$dashboardId");

        $this->redis
            ->expects($this->at(1))
            ->method('del')
            ->with("dashboard:meta:$dashboardId");

        $this->redis
            ->expects($this->at(2))
            ->method('sRem')
            ->with("dashboard:ids", $dashboardId);

        $this->subject->delete($dashboardId);
    }

    public function testUpdateMetadata()
    {
        $dashboardId = 42;
        $payload = [
            'name' => 'fooname'
        ];

        $this->redis
            ->expects($this->once())
            ->method('hmSet')
            ->with("dashboard:meta:$dashboardId", $payload);

        $this->subject->updateMetadata($dashboardId, $payload);
    }

    public function testAddDashboard()
    {
        $dashboardId = 42;
        $metadata = [
            'name' => 'fooname'
        ];

        $this->redis
            ->expects($this->once())
            ->method('sAdd')
            ->with(DashboardGateway::IDS_KEY, $dashboardId);

        $this->redis
            ->expects($this->once())
            ->method('hmSet')
            ->with("dashboard:meta:$dashboardId", $metadata);

        $this->subject->addDashboard($dashboardId, $metadata);
    }
}
