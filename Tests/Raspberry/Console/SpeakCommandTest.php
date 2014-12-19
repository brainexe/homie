<?php

namespace Tests\Raspberry\Console\SpeakCommand;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Console\SpeakCommand;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers Raspberry\Console\SpeakCommand
 */
class SpeakCommandTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SpeakCommand
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    public function setUp()
    {
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->subject = new SpeakCommand();
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testExecute()
    {
        $text = 'nice text';

        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $espeak_vo = new EspeakVO($text);
        $event = new EspeakEvent($espeak_vo);

        $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatchEvent')
        ->with($event);

        $commandTester->execute(['text' => $text]);
    }
}
