<?php

namespace Tests\Homie\Switches\Controller;


use Homie\Switches\Controller\Jobs;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

use Homie\Switches\SwitchChangeEvent;
use Homie\Switches\VO\RadioVO;
use Symfony\Component\HttpFoundation\Request;
use Homie\Switches\Switches;
use Homie\Switches\Job;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @covers Homie\Switches\Controller\Jobs
 */
class JobsTest extends TestCase
{

    /**
     * @var Jobs
     */
    private $subject;

    /**
     * @var Switches|MockObject
     */
    private $switches;

    /**
     * @var Job|MockObject
     */
    private $job;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->switches   = $this->createMock(Switches::class);
        $this->job        = $this->createMock(Job::class);
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new Jobs($this->switches, $this->job);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testSetStatus()
    {
        $request  = new Request();
        $switchId = 10;
        $status   = true;
        $switchVo = new RadioVO();
        $event    = new SwitchChangeEvent($switchVo, $status);

        $this->switches
            ->expects($this->once())
            ->method('get')
            ->with($switchId)
            ->willReturn($switchVo);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $actual = $this->subject->setStatus($request, $switchId, $status);

        $this->assertEquals(true, $actual);
    }

    public function testAddJob()
    {
        $switchId    = 10;
        $status     = false;
        $timeString = 'time';

        $switch = new RadioVO();

        $request = new Request();
        $request->request->set('switchId', $switchId);
        $request->request->set('status', $status);
        $request->request->set('time', $timeString);

        $this->switches
            ->expects($this->once())
            ->method('get')
            ->with($switchId)
            ->willReturn($switch);

        $this->job
            ->expects($this->once())
            ->method('addJob')
            ->with($switch, $timeString, $status);

        $actual = $this->subject->addJob($request);

        $this->assertTrue($actual);
    }
}
