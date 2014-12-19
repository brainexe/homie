<?php

namespace Tests\Raspberry\Controller\EggTimerController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Controller\EggTimerController;
use Raspberry\EggTimer\EggTimer;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers Raspberry\Controller\EggTimerController
 */
class EggTimerControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var EggTimerController
     */
    private $subject;

    /**
     * @var EggTimer|MockObject
     */
    private $mockEggTimer;

    public function setUp()
    {
        $this->mockEggTimer = $this->getMock(EggTimer::class, [], [], '', false);

        $this->subject = new EggTimerController($this->mockEggTimer);
    }

    public function testIndex()
    {
        $jobs = [];

        $this->mockEggTimer
        ->expects($this->once())
        ->method('getJobs')
        ->will($this->returnValue($jobs));

        $actual_result = $this->subject->index();

        $expected = [
        'jobs' => $jobs
        ];

        $this->assertEquals($expected, $actual_result);
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
        ->will($this->returnValue($jobs));

        $actual_result = $this->subject->add($request);

        $this->assertEquals($jobs, $actual_result);
    }

    public function testDeleteEggTimer()
    {
        $request = new Request();
        $job_id = 10;

        $jobs = [];

        $this->mockEggTimer
        ->expects($this->once())
        ->method('deleteJob')
        ->with($job_id);

        $this->mockEggTimer
        ->expects($this->once())
        ->method('getJobs')
        ->will($this->returnValue($jobs));

        $actual_result = $this->subject->deleteEggTimer($request, $job_id);

        $this->assertEquals($jobs, $actual_result);
    }
}
