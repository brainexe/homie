<?php

namespace Tests\Homie\Sensors\Command;

use Homie\Sensors\GetValue\Event;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\Command\Cron;
use Homie\Sensors\Definition;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorBuilder;
use Homie\Sensors\SensorVO;
use Homie\Sensors\Builder;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\Time;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers Homie\Sensors\Command\Cron
 */
class CronTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Cron
     */
    private $subject;

    /**
     * @var SensorGateway|MockObject
     */
    private $gateway;

    /**
     * @var SensorBuilder|MockObject
     */
    private $builder;

    /**
     * @var Builder|MockObject
     */
    private $voBuilder;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    /**
     * @var Time|MockObject
     */
    private $mockTime;

    /**
     * @var integer
     */
    private $nodeId = 1;

    public function setUp()
    {
        $this->gateway       = $this->getMock(SensorGateway::class, [], [], '', false);
        $this->builder       = $this->getMock(SensorBuilder::class, [], [], '', false);
        $this->voBuilder     = $this->getMock(Builder::class, [], [], '', false);
        $this->dispatcher    = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->mockTime      = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new Cron(
            $this->gateway,
            $this->builder,
            $this->voBuilder,
            $this->dispatcher,
            $this->nodeId
        );
        $this->subject->setTime($this->mockTime);
    }

    public function testExecute()
    {
        $now = 1000;
        $sensors = [
            $sensorRaw = []
        ];

        $sensor = new SensorVO();
        $sensor->sensorId = 10;
        $sensor->type     = $type = 'type';
        $sensor->pin      = $pin = 12;
        $sensor->name     = 'name';

        $definition = new Definition();
        $definition->name = 'type';

        $this->mockTime
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensors);

        $this->voBuilder
            ->expects($this->once())
            ->method('buildFromArray')
            ->with($sensorRaw)
            ->willReturn($sensor);

        $event = new Event($sensor);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);
        $this->dispatcher
            ->expects($this->exactly(2))
            ->method('addListener');

        $application = new Application();
        $application->add($this->subject);
        $input = ['--force'];
        $commandTester = new CommandTester($this->subject);
        $commandTester->execute($input);
    }
}
