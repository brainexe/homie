<?php

namespace Tests\Homie\Radio\Controller;


use Homie\Radio\Controller\Jobs;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

use Homie\Radio\SwitchChangeEvent;
use Homie\Radio\VO\RadioVO;
use Symfony\Component\HttpFoundation\Request;
use Homie\Radio\Switches;
use Homie\Radio\Job;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @covers Homie\Radio\Controller\Jobs
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
        $this->switches   = $this->getMock(Switches::class, [], [], '', false);
        $this->job        = $this->getMock(Job::class, [], [], '', false);
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

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
