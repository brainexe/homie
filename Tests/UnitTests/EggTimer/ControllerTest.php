<?php

namespace Tests\Homie\EggTimer;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\EggTimer\Controller;
use Homie\EggTimer\EggTimer;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers Homie\EggTimer\Controller
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
    private $timer;

    public function setUp()
    {
        $this->timer   = $this->getMock(EggTimer::class, [], [], '', false);
        $this->subject = new Controller($this->timer);
    }

    public function testAdd()
    {
        $time = 'time';
        $text = 'text';

        $request = new Request();
        $request->request->set('text', $text);
        $request->request->set('time', $time);

        $this->timer
            ->expects($this->once())
            ->method('addNewJob')
            ->with($time, $text);

        $jobs = ['jobs'];
        $this->timer
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

        $this->timer
            ->expects($this->once())
            ->method('deleteJob')
            ->with($jobId);

        $this->timer
            ->expects($this->once())
            ->method('getJobs')
            ->willReturn($jobs);

        $actualResult = $this->subject->deleteEggTimer($request, $jobId);

        $this->assertEquals($jobs, $actualResult);
    }
}
