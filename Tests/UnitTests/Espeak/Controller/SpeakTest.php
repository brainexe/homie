<?php

namespace Tests\Homie\Espeak\Controller;

use BrainExe\Core\MessageQueue\Job;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Espeak\Controller\Speak;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Symfony\Component\HttpFoundation\Request;
use BrainExe\Core\Util\TimeParser;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @covers \Homie\Espeak\Controller\Speak
 */
class SpeakTest extends TestCase
{

    /**
     * @var Speak
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

        $this->subject = new Speak($this->timeParser);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testSpeak()
    {
        $request   = new Request();
        $speaker   = 'speaker';
        $text      = 'text';
        $volume    = 120;
        $speed     = 80;
        $delayRaw  = 'delay_row';
        $timestamp = 10;

        $request->request->set('speaker', $speaker);
        $request->request->set('text', $text);
        $request->request->set('volume', $volume);
        $request->request->set('speed', $speed);
        $request->request->set('delay', $delayRaw);

        $this->timeParser
            ->expects($this->once())
            ->method('parseString')
            ->with($delayRaw)
            ->willReturn($timestamp);

        $espeakVo = new EspeakVO($text, $volume, $speed, $speaker);
        $event    = new EspeakEvent($espeakVo);

        $job = $this->createMock(Job::class);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event, $timestamp)
            ->willReturn($job);

        $actual = $this->subject->speak($request);

        $this->assertEquals($job, $actual);
    }
}
