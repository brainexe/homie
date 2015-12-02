<?php

namespace Tests\Homie\EggTimer\EggTimer;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\TimeParser;
use Homie\EggTimer\EggTimer;
use Homie\EggTimer\EggTimerEvent;
use Homie\Espeak\EspeakVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

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
        $this->timeParser = $this->getMock(TimeParser::class, [], [], '', false);
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

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
}
