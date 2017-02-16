<?php

namespace Tests\Homie\Sensors\Command;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Sensors\Command\Aggregated;
use Homie\Sensors\Aggregate\AggregateEvent;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class AggregatedTest extends TestCase
{

    /**
     * @var Aggregated
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->createMock(EventDispatcher::class);
        $this->subject = new Aggregated();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);

        $identifier = 'mockIdentifier';
        $value      = 'mockValue';

        $event = new AggregateEvent($identifier, $value);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $commandTester->execute(['identifier' => $identifier, 'value' => $value]);
    }
}
