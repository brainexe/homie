<?php

namespace Tests\Homie\EggTimer\EggTimer;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\MessageQueue\Job;
use BrainExe\Core\Util\TimeParser;
use Homie\EggTimer\EggTimer;
use Homie\EggTimer\EggTimerEvent;
use Homie\Espeak\EspeakVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

class EggTimerTest extends TestCase
{

    /**
     * @var EggTimer
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
        $this->timeParser = $this->createMock(TimeParser::class);
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new EggTimer($this->timeParser);
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

        $job = $this->createMock(Job::class);
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event, $timestamp)
            ->willReturn($job);

        $actual = $this->subject->addNewJob($time, $text);
        $this->assertEquals($job, $actual);
    }

    public function testAddNewJobWithText()
    {
        $time = 'time';
        $text = 'text';

        $timestamp = 100;

        $espeakVo = new EspeakVO($text);
        $event = new EggTimerEvent($espeakVo);
        $job = $this->createMock(Job::class);

        $this->timeParser
            ->expects($this->once())
            ->method('parseString')
            ->with($time)
            ->willReturn($timestamp);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event, $timestamp)
            ->willReturn($job);

        $actual = $this->subject->addNewJob($time, $text);
        $this->assertEquals($job, $actual);
    }
}
