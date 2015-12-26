<?php

namespace Tests\Homie\Arduino;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Arduino\Command;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Arduino\SerialEvent;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers Homie\Arduino\Command
 */
class CommandTest extends TestCase
{

    /**
     * @var Command
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->subject = new Command();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    /**
     * @dataProvider provideEvents
     * @param string $action
     * @param int $pin
     * @param string $value
     * @param SerialEvent $expectedEvent
     */
    public function testExecute($action, $pin, $value, SerialEvent $expectedEvent)
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($expectedEvent);

        $commandTester->execute([
            'action' => $action,
            'pin'  => $pin,
            'value' => $value
        ]);
    }

    public function provideEvents()
    {
        return [
            [SerialEvent::DIGITAL, 1, 2, new SerialEvent(SerialEvent::DIGITAL, 1, 2)],
            [SerialEvent::ANALOG, 1, 200, new SerialEvent(SerialEvent::ANALOG, 1, 200)],
        ];
    }
}
