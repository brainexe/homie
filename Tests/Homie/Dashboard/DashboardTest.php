<?php

namespace Tests\Homie\Dashboard\Dashboard;

use BrainExe\Core\Util\IdGenerator;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Dashboard\AbstractWidget;
use Homie\Dashboard\Dashboard;
use Homie\Dashboard\DashboardGateway;
use Homie\Dashboard\WidgetFactory;

/**
 * @covers Homie\Dashboard\Dashboard
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

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    public function setUp()
    {
        $this->widgetFactory = $this->getMock(WidgetFactory::class, [], [], '', false);
        $this->gateway       = $this->getMock(DashboardGateway::class, [], [], '', false);
        $this->idGenerator   = $this->getMock(IdGenerator::class, [], [], '', false);

        $this->subject = new Dashboard($this->gateway, $this->widgetFactory);
        $this->subject->setIdGenerator($this->idGenerator);
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
        $dashboardId = 42;
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
            ->with($dashboardId, $payload);

        $this->subject->addWidget($dashboardId, $type, $payload);
    }

    public function testAddWidgetWithoutDashboard()
    {
        $dashboardId = 11880;
        $type = 'type';
        $payload = [];
        $payload['type'] = $type;

        $widget = $this->getMock(AbstractWidget::class);

        $this->widgetFactory
            ->expects($this->once())
            ->method('getWidget')
            ->with($type)
            ->willReturn($widget);

        $this->idGenerator
            ->expects($this->once())
            ->method('generateRandomNumericId')
            ->willReturn($dashboardId);

        $widget
            ->expects($this->once())
            ->method('validate')
            ->with($payload);

        $this->gateway
            ->expects($this->once())
            ->method('addWidget')
            ->with($dashboardId, $payload);

        $this->gateway
            ->expects($this->once())
            ->method('addDashboard')
            ->with($dashboardId, [
                'name' => 'Dashboard'
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
        $payload     = ['payload'];

        $this->gateway
            ->expects($this->once())
            ->method('updateMetadata')
            ->willReturn($dashboardId, $payload);

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
            ->willReturn($dashboardId, $widgetId, $payload);

        $this->subject->updateWidget($dashboardId, $widgetId, $payload);
    }
}
