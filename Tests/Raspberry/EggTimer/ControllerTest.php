<?php

namespace Tests\Raspberry\EggTimer;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\EggTimer\Controller;
use Raspberry\EggTimer\EggTimer;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers Raspberry\EggTimer\Controller
 */
class ControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var EggTimer|MockObject
     */
    private $mockEggTimer;

    public function setUp()
    {
        $this->mockEggTimer = $this->getMock(EggTimer::class, [], [], '', false);

        $this->subject = new Controller($this->mockEggTimer);
    }

    public function testIndex()
    {
        $jobs = [];

        $this->mockEggTimer
            ->expects($this->once())
            ->method('getJobs')
            ->willReturn($jobs);

        $actualResult = $this->subject->index();

        $expected = [
            'jobs' => $jobs
        ];

        $this->assertEquals($expected, $actualResult);
    }

    public function testAdd()
    {
        $time = 'time';
        $text = 'text';

        $request = new Request();
        $request->request->set('text', $text);
        $request->request->set('time', $time);

        $this->mockEggTimer
            ->expects($this->once())
            ->method('addNewJob')
            ->with($time, $text);

        $jobs = ['jobs'];
        $this->mockEggTimer
            ->expects($this->once())
            ->method('getJobs')
            ->willReturn($jobs);

        $actualResult = $this->subject->add($request);

        $this->assertEquals($jobs, $actualResult);
    }

    public function testDeleteEggTimer()
    {
        $request = new Request();
        $jobId = 10;

        $jobs = [];

        $this->mockEggTimer
            ->expects($this->once())
            ->method('deleteJob')
            ->with($jobId);

        $this->mockEggTimer
            ->expects($this->once())
            ->method('getJobs')
            ->willReturn($jobs);

        $actualResult = $this->subject->deleteEggTimer($request, $jobId);

        $this->assertEquals($jobs, $actualResult);
    }
}
