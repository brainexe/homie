<?php

namespace Tests\Homie\Dashboard;

use ArrayIterator;
use Homie\Dashboard\DashboardVo;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Dashboard\Controller;
use Symfony\Component\HttpFoundation\Request;
use Homie\Dashboard\Dashboard;

/**
 * @covers \Homie\Dashboard\Controller
 */
class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var Dashboard|MockObject
     */
    private $dashboard;

    public function setUp()
    {
        $this->dashboard = $this->createMock(Dashboard::class);
        $this->subject   = new Controller($this->dashboard);
    }

    public function testIndex()
    {
        $widgets    = ['widgets'];

        $this->dashboard
            ->expects($this->once())
            ->method('getAvailableWidgets')
            ->willReturn($widgets);

        $actualResult = $this->subject->metadata();

        $expectedResult = [
            'widgets'   => $widgets
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testDashboards()
    {
        $dashboards = ['dashboards'];

        $this->dashboard
            ->expects($this->once())
            ->method('getDashboards')
            ->willReturn(new ArrayIterator($dashboards));

        $actualResult = $this->subject->dashboard();

        $expectedResult = [
            'dashboards' => $dashboards,
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAddWidget()
    {
        $type          = 'type';
        $dashboardId   = 0;

        $payload   = ['payload'];
        $dashboard = new DashboardVo();

        $request = new Request();
        $request->request->set('type', $type);
        $request->request->set('payload', $payload);

        $this->dashboard
            ->expects($this->once())
            ->method('addWidget')
            ->with($dashboardId, $type, $payload)
            ->willReturn($dashboard);

        $actualResult = $this->subject->addWidget($request);

        $this->assertEquals($dashboard, $actualResult);
    }

    public function testUpdateDashboard()
    {
        $dashboardId = 1212;
        $dashboard   = new DashboardVo();

        $request = new Request();
        $request->request->set('foo', 'bar');

        $this->dashboard
            ->expects($this->once())
            ->method('updateDashboard')
            ->with($dashboardId, [
                'foo' => 'bar'
            ])
            ->willReturn($dashboard);

        $actualResult = $this->subject->updateDashboard($request, $dashboardId);

        $this->assertEquals($dashboard, $actualResult);
    }

    public function testDeleteWidget()
    {
        $widgetId    = 12;
        $dashboardId = 1;
        $dashboard   = new DashboardVo();

        $request = new Request();

        $this->dashboard
            ->expects($this->once())
            ->method('deleteWidget')
            ->with($dashboardId, $widgetId)
            ->willReturn($dashboard);

        $this->dashboard
            ->expects($this->once())
            ->method('getDashboard')
            ->with($dashboardId)
            ->willReturn($dashboard);

        $actualResult = $this->subject->deleteWidget($request, $dashboardId, $widgetId);

        $this->assertEquals($dashboard, $actualResult);
    }

    public function testDelete()
    {
        $dashboardId = 12;
        $request = new Request();

        $this->dashboard
            ->expects($this->once())
            ->method('delete')
            ->with($dashboardId);

        $actualResult = $this->subject->deleteDashboard($request, $dashboardId);

        $this->assertTrue($actualResult);
    }

    public function testUpdateWidget()
    {
        $dashboardId = 100;
        $widgetId    = 222;
        $dashboard   = new DashboardVo();

        $request = new Request();
        $request->request->set('foo', 'bar');

        $this->dashboard
            ->expects($this->once())
            ->method('updateWidget')
            ->with($dashboardId, $widgetId, [
                'foo' => 'bar'
            ]);

        $this->dashboard
            ->expects($this->once())
            ->method('getDashboard')
            ->with($dashboardId)
            ->willReturn($dashboard);

        $actual = $this->subject->updateWidget($request, $dashboardId, $widgetId);

        $this->assertEquals($dashboard, $actual);
    }
}
