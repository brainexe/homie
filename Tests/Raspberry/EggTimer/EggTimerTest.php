<?php

namespace Tests\Raspberry\EggTimer\EggTimer;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\EggTimer\EggTimer;
use BrainExe\MessageQueue\MessageQueueGateway;
use BrainExe\Core\Util\TimeParser;
use BrainExe\Core\Util\Time;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Raspberry\EggTimer\EggTimerEvent;
use Raspberry\Espeak\EspeakVO;

class EggTimerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var EggTimer
     */
    private $subject;

    /**
     * @var MessageQueueGateway|MockObject
     */
    private $mockMessageQueueGateway;

    /**
     * @var TimeParser|MockObject
     */
    private $mockTimeParser;

    /**
     * @var Time|MockObject
     */
    private $mockTime;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    public function setUp()
    {
        $this->mockMessageQueueGateway = $this->getMock(MessageQueueGateway::class, [], [], '', false);
        $this->mockTimeParser          = $this->getMock(TimeParser::class, [], [], '', false);
        $this->mockTime                = $this->getMock(Time::class, [], [], '', false);
        $this->mockEventDispatcher     = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new EggTimer($this->mockMessageQueueGateway, $this->mockTimeParser);
        $this->subject->setTime($this->mockTime);
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testAddNewJobWithoutText()
    {
        $time = 'time';
        $text = '';

        $timestamp = 100;

        $espeak_vo = null;
        $event = new EggTimerEvent($espeak_vo);

        $this->mockTimeParser
        ->expects($this->once())
        ->method('parseString')
        ->with($time)
        ->will($this->returnValue($timestamp));

        $this->mockEventDispatcher
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

        $espeak_vo = new EspeakVO($text);
        $event = new EggTimerEvent($espeak_vo);

        $this->mockTimeParser
        ->expects($this->once())
        ->method('parseString')
        ->with($time)
        ->will($this->returnValue($timestamp));

        $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatchInBackground')
        ->with($event, $timestamp);

        $this->subject->addNewJob($time, $text);
    }

    public function testDeleteJob()
    {
        $jobId = 10;

        $this->mockMessageQueueGateway
        ->expects($this->once())
        ->method('deleteEvent')
        ->with($jobId, EggTimerEvent::DONE);

        $this->subject->deleteJob($jobId);
    }

    public function testGetJobs()
    {
        $now = 1000;
        $jobs = [];

        $this->mockTime
        ->expects($this->once())
        ->method('now')
        ->will($this->returnValue($now));

        $this->mockMessageQueueGateway
        ->expects($this->once())
        ->method('getEventsByType')
        ->with(EggTimerEvent::DONE, $now)
        ->will($this->returnValue($jobs));

        $actualResult = $this->subject->getJobs();

        $this->assertEquals($jobs, $actualResult);
    }
}
