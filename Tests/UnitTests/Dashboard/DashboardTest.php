<?php

namespace Tests\Homie\Dashboard;

use BrainExe\Core\Util\IdGenerator;
use Homie\Dashboard\DashboardVo;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Dashboard\Dashboard;
use Homie\Dashboard\DashboardGateway;
use Homie\Dashboard\WidgetFactory;

/**
 * @covers Homie\Dashboard\Dashboard
 */
class DashboardTest extends TestCase
{

    /**
     * @var Dashboard
     */
    private $subject;

    /**
     * @var WidgetFactory|MockObject
     */
    private $widgetFactory;

    /**
     * @var DashboardGateway|MockObject
     */
    private $gateway;

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    public function setUp()
    {
        $this->widgetFactory = $this->createMock(WidgetFactory::class);
        $this->gateway       = $this->createMock(DashboardGateway::class);
        $this->idGenerator   = $this->createMock(IdGenerator::class);

        $this->subject = new Dashboard($this->gateway, $this->widgetFactory);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testGetDashboard()
    {
        $dashboard = new DashboardVo();
        $userId    = 10;

        $this->gateway
            ->expects($this->once())
            ->method('getDashboard')
            ->willReturn($dashboard);

        $actual = $this->subject->getDashboard($userId);

        $this->assertEquals($dashboard, $actual);
    }

    public function testGetAvailableWidgets()
    {
        $widgets = [];

        $this->widgetFactory
            ->expects($this->once())
            ->method('getAvailableWidgets')
            ->willReturn($widgets);

        $actual = $this->subject->getAvailableWidgets();

        $this->assertEquals($widgets, $actual);
    }

    public function testAddWidget()
    {
        $dashboardId = 42;
        $type = 'type';
        $payload = [];
        $payload['type'] = $type;

        $this->gateway
            ->expects($this->once())
            ->method('addWidget')
            ->with($dashboardId, $payload);

        $this->subject->addWidget($dashboardId, $type, $payload);
    }

    public function testAddWidgetWithoutDashboard()
    {
        $dashboardId = 11880;
        $type = 'type';
        $payload = [];
        $payload['type'] = $type;

        $this->idGenerator
            ->expects($this->once())
            ->method('generateUniqueId')
            ->willReturn($dashboardId);

        $this->gateway
            ->expects($this->once())
            ->method('addWidget')
            ->with($dashboardId, $payload);

        $this->gateway
            ->expects($this->once())
            ->method('addDashboard')
            ->with($dashboardId, [
                'name' => 'Dashboard - 11880'
            ]);

        $this->subject->addWidget(null, $type, $payload);
    }

    public function testDeleteWidget()
    {
        $widgetId = 1;
        $userId   = 42;

        $this->gateway
            ->expects($this->once())
            ->method('deleteWidget')
            ->with($userId, $widgetId);

        $this->subject->deleteWidget($userId, $widgetId);
    }

    public function testGetDashboards()
    {
        $dashboards = [new DashboardVo()];

        $this->gateway
            ->expects($this->once())
            ->method('getDashboards')
            ->willReturn($dashboards);

        $actual = $this->subject->getDashboards();

        $this->assertEquals($dashboards, $actual);
    }

    public function testUpdateDashboard()
    {
        $dashboardId = 1233;
        $payload     = ['payload'];

        $this->gateway
            ->expects($this->once())
            ->method('updateMetadata')
            ->with($dashboardId, $payload);

        $this->subject->updateDashboard($dashboardId, $payload);
    }

    public function testDelete()
    {
        $dashboardId = 1233;

        $this->gateway
            ->expects($this->once())
            ->method('delete')
            ->willReturn($dashboardId);

        $this->subject->delete($dashboardId);
    }

    public function testUpdateWidget()
    {
        $dashboardId = 1233;
        $widgetId    = 999;
        $payload     = ['payload'];

        $this->gateway
            ->expects($this->once())
            ->method('updateWidget')
            ->with($dashboardId, $widgetId, $payload);

        $this->subject->updateWidget($dashboardId, $widgetId, $payload);
    }
}
