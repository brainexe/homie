<?php

namespace Tests\Raspberry\Dashboard;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Dashboard\Controller;
use Symfony\Component\HttpFoundation\Request;
use Raspberry\Dashboard\Dashboard;

/**
 * @Covers Raspberry\Dashboard\Controller
 */
class ControllerTest extends PHPUnit_Framework_TestCase
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
        $this->dashboard = $this->getMock(Dashboard::class, [], [], '', false);
        $this->subject   = new Controller($this->dashboard);
    }

    public function testIndex()
    {
        $userId = 0;

        $dashboard = ['dashboard'];
        $widgets   = ['widgets'];

        $this->dashboard
            ->expects($this->once())
            ->method('getDashboard')
            ->with($userId)
            ->willReturn($dashboard);

        $this->dashboard
            ->expects($this->once())
            ->method('getAvailableWidgets')
            ->willReturn($widgets);

        $request = new Request();
        $actualResult = $this->subject->index($request);

        $expectedResult = [
            'dashboard' => $dashboard,
            'widgets'   => $widgets
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAddWidget()
    {
        $type = 'type';
        $userId   = 0;

        $payload   = ['payload'];
        $dashboard = ['dashboard'];

        $request = new Request();
        $request->request->set('type', $type);
        $request->request->set('payload', $payload);

        $this->dashboard
            ->expects($this->once())
            ->method('addWidget')
            ->with($userId, $type, $payload);

        $this->dashboard
            ->expects($this->once())
            ->method('getDashboard')
            ->with($userId)
            ->willReturn($dashboard);

        $actualResult = $this->subject->addWidget($request);

        $this->assertEquals($dashboard, $actualResult);
    }

    public function testDeleteWidget()
    {
        $widgetId = 12;
        $userId   = 0;

        $dashboard = ['dashboard'];

        $request = new Request();
        $request->request->set('widget_id', $widgetId);

        $this->dashboard
            ->expects($this->once())
            ->method('deleteWidget')
            ->with($userId, $widgetId)
            ->willReturn($dashboard);

        $this->dashboard
            ->expects($this->once())
            ->method('getDashboard')
            ->with($userId)
            ->willReturn($dashboard);

        $actualResult = $this->subject->deleteWidget($request);

        $expectedResult = $dashboard;
        $this->assertEquals($expectedResult, $actualResult);
    }
}
