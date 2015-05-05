<?php

namespace Tests\Homie\EggTimer\EggTimer;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\EggTimer\EggTimer;
use BrainExe\MessageQueue\Gateway;
use BrainExe\Core\Util\TimeParser;
use BrainExe\Core\Util\Time;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\EggTimer\EggTimerEvent;
use Homie\Espeak\EspeakVO;

class EggTimerTest extends TestCase
{

    /**
     * @var EggTimer
     */
    private $subject;

    /**
     * @var Gateway|MockObject
     */
    private $gateway;

    /**
     * @var TimeParser|MockObject
     */
    private $timeParser;

    /**
     * @var Time|MockObject
     */
    private $time;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->gateway = $this->getMock(Gateway::class, [], [], '', false);
        $this->timeParser          = $this->getMock(TimeParser::class, [], [], '', false);
        $this->time                = $this->getMock(Time::class, [], [], '', false);
        $this->dispatcher          = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new EggTimer($this->gateway, $this->timeParser);
        $this->subject->setTime($this->time);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testAddNewJobWithoutText()
    {
        $time = 'time';
        $text = '';

        $timestamp = 100;

        $espeakVo = null;
        $event = new EggTimerEvent($espeakVo);

        $this->timeParser
            ->expects($this->once())
            ->method('parseString')
            ->with($time)
            ->willReturn($timestamp);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event, $timestamp);

        $this->subject->addNewJob($time, $text);
    }

    public function testAddNewJobWithText()
    {
        $time = 'time';
        $text = 'text';

        $timestamp = 100;

        $espeakVo = new EspeakVO($text);
        $event = new EggTimerEvent($espeakVo);

        $this->timeParser
            ->expects($this->once())
            ->method('parseString')
            ->with($time)
            ->willReturn($timestamp);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event, $timestamp);

        $this->subject->addNewJob($time, $text);
    }

    public function testDeleteJob()
    {
        $jobId = 10;

        $this->gateway
            ->expects($this->once())
            ->method('deleteEvent')
            ->with($jobId, EggTimerEvent::DONE);

        $this->subject->deleteJob($jobId);
    }

    public function testGetJobs()
    {
        $now  = 1000;
        $jobs = [];

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $this->gateway
            ->expects($this->once())
            ->method('getEventsByType')
            ->with(EggTimerEvent::DONE, $now)
            ->willReturn($jobs);

        $actualResult = $this->subject->getJobs();

        $this->assertEquals($jobs, $actualResult);
    }
}
