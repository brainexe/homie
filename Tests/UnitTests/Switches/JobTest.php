<?php

namespace Homie\Tests\Switches;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\TimeParser;
use Homie\Switches\SwitchChangeEvent;
use Homie\Switches\Job;
use Homie\Switches\VO\RadioVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

class JobTest extends TestCase
{

    /**
     * @var Job
     */
    private $subject;

    /**
     * @var TimeParser|MockObject
     */
    private $timeParser;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->timeParser = $this->getMock(TimeParser::class);
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new Job($this->timeParser);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testAddJob()
    {
        $timeString = '1h';
        $timestamp  = 1345465;
        $status     = true;

        $radioVo = new RadioVO();
        $radioVo->switchId = 1;

        $this->timeParser
            ->expects($this->once())
            ->method('parseString')
            ->with($timeString)
            ->willReturn($timestamp);

        $event = new SwitchChangeEvent($radioVo, $status);
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event, $timestamp);

        $this->subject->addJob($radioVo, $timeString, $status);
    }
}
