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
            $widgetId = 10 => json_encode($payload),
            'name' => 'mockName'
        ];

        $this->redis
            ->expects($this->once())
            ->method('hGetAll')
            ->with("dashboard:$dashboardId")
            ->willReturn($widgetsRaw);

        $actualResult = $this->subject->getDashboard($dashboardId);

        $dashboard = new DashboardVo();

        $widget = [];
        $widget['id']      = $widgetId;
        $widget['open']    = true;
        $widget['payload'] = 'payload';

        $dashboard->widgets = [$widget];
        $dashboard->dashboardId = $dashboardId;
        $dashboard->name = 'mockName';

        $this->assertEquals($dashboard, $actualResult);
    }

    public function testGetDashboards()
    {
        $dashboardId = 42;

        $payload    = [
            'payload' => 'payload'
        ];

        $widgetsRaw = [
            $widgetId = 10 => json_encode($payload),
            'name' => 'mockName'
        ];

        $this->redis
            ->expects($this->once())
            ->method('sMembers')
            ->with("dashboard_ids")
            ->willReturn([$dashboardId]);

        $this->redis
            ->expects($this->once())
            ->method('hGetAll')
            ->with("dashboard:$dashboardId")
            ->willReturn($widgetsRaw);

        $actualResult = $this->subject->getDashboards();

        $dashboard = new DashboardVo();

        $widget = [];
        $widget['id']      = $widgetId;
        $widget['open']    = true;
        $widget['payload'] = 'payload';

        $dashboard->widgets = [$widget];
        $dashboard->dashboardId = $dashboardId;
        $dashboard->name = 'mockName';

        $this->assertEquals([$dashboardId => $dashboard], $actualResult);
    }

    public function testAddWidget()
    {
        $dashboardId          = 42;
        $type            = 'type';
        $payload         = [];
        $payload['type'] = $type;

        $newId = 11880;
        $this->idGenerator
            ->expects($this->once())
            ->method('generateRandomNumericId')
            ->willReturn($newId);

        $this->redis
            ->expects($this->once())
            ->method('HSET')
            ->with("dashboard:$dashboardId", $newId, json_encode($payload));

        $this->redis
            ->expects($this->once())
            ->method('sAdd')
            ->with("dashboard_ids", $dashboardId);

        $this->subject->addWidget($dashboardId, $payload);
    }

    public function testDeleteWidget()
    {
        $widgetId    = 1;
        $dashboardId = 42;

        $this->redis
            ->expects($this->once())
            ->method('HDEL')
            ->with("dashboard:$dashboardId", $widgetId);

        $this->subject->deleteWidget($dashboardId, $widgetId);
    }

    public function testDelete()
    {
        $dashboardId = 42;

        $this->redis
            ->expects($this->once())
            ->method('del')
            ->with("dashboard:$dashboardId");

        $this->redis
            ->expects($this->once())
            ->method('sRem')
            ->with("dashboard_ids", $dashboardId);

        $this->subject->delete($dashboardId);
    }

    public function testUpdateDashboard()
    {
        $dashboardId = 42;
        $name = 'fooname';

        $this->redis
            ->expects($this->once())
            ->method('hSet')
            ->with("dashboard:$dashboardId", 'name', $name);

        $this->subject->updateDashboard($dashboardId, $name);
    }
}
