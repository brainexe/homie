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
class DashboardTest extends PHPUnit_Framework_TestCase
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

    public function setUp()
    {
        $this->widgetFactory = $this->getMock(WidgetFactory::class, [], [], '', false);
        $this->gateway       = $this->getMock(DashboardGateway::class, [], [], '', false);

        $this->subject = new Dashboard($this->gateway, $this->widgetFactory);
    }

    public function testGetDashboard()
    {
        $dashboard = [];
        $userId    = 10;

        $this->gateway
            ->expects($this->once())
            ->method('getDashboard')
            ->willReturn($dashboard);

        $actualResult = $this->subject->getDashboard($userId);

        $this->assertEquals($dashboard, $actualResult);
    }

    public function testGetAvailableWidgets()
    {
        $widgets = [];

        $this->widgetFactory
            ->expects($this->once())
            ->method('getAvailableWidgets')
            ->willReturn($widgets);

        $actualResult = $this->subject->getAvailableWidgets();

        $this->assertEquals($widgets, $actualResult);
    }

    public function testAddWidget()
    {
        $userId = 42;
        $type = 'type';
        $payload = [];
        $payload['type'] = $type;

        $widget = $this->getMock(AbstractWidget::class);

        $this->widgetFactory
            ->expects($this->once())
            ->method('getWidget')
            ->with($type)
            ->willReturn($widget);

        $widget
            ->expects($this->once())
            ->method('validate')
            ->with($payload);

        $this->gateway
            ->expects($this->once())
            ->method('addWidget')
            ->with($userId, $payload);

        $this->subject->addWidget($userId, $type, $payload);
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
        $dashboards = ['dashboards'];

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
        $name        = 'name';

        $this->gateway
            ->expects($this->once())
            ->method('updateDashboard')
            ->willReturn($dashboardId, $name);

        $this->subject->updateDashboard($dashboardId, $name);
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
}
