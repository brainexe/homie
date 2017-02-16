<?php

namespace Tests\Homie\Sensors\Command;

use Homie\Sensors\GetValue\GetSensorValueEvent;
use Homie\Sensors\SensorValueEvent;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\Command\Cron;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorBuilder;
use Homie\Sensors\SensorVO;
use Homie\Sensors\Builder;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\Time;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \Homie\Sensors\Command\Cron
 */
class CronTest extends TestCase
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
    private $time;

    /**
     * @var integer
     */
    private $nodeId = 1;

    public function setUp()
    {
        $this->gateway    = $this->createMock(SensorGateway::class);
        $this->builder    = $this->createMock(SensorBuilder::class);
        $this->voBuilder  = $this->createMock(Builder::class);
        $this->dispatcher = $this->createMock(EventDispatcher::class);
        $this->time       = $this->createMock(Time::class);

        $this->subject = new Cron(
            $this->gateway,
            $this->builder,
            $this->voBuilder,
            $this->dispatcher,
            $this->nodeId
        );
        $this->subject->setTime($this->time);
    }

    public function testExecute()
    {
        $now = 1000;
        $sensors = [
            $sensorRaw = []
        ];

        $sensor            = new SensorVO();
        $sensor->sensorId  = 10;
        $sensor->type      = $type = 'type';
        $sensor->parameter = 12;
        $sensor->name      = 'name';

        $this->time
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

        $event = new GetSensorValueEvent($sensor);

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

    public function testExecuteWithoutInterval()
    {
        $now = 1000;
        $sensors = [
            $sensorRaw = []
        ];

        $sensor = new SensorVO();
        $sensor->interval = -1;

        $this->time
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

        $this->dispatcher
            ->expects($this->exactly(2))
            ->method('addListener');

        $application = new Application();
        $application->add($this->subject);
        $input = ['--force'];
        $commandTester = new CommandTester($this->subject);
        $commandTester->execute($input);
    }

    public function testHandleEvent()
    {
        $sensorVo = new SensorVO();
        $sensorVo->sensorId = 42;
        $sensorVo->type = 'mockType';
        $sensorVo->name = 'mockName';
        $sensorVo->interval = 10000;

        $value = 10;
        $valueFormatted = '10°';
        $timestamp = 1000;

        $event = new SensorValueEvent(
            SensorValueEvent::VALUE,
            $sensorVo,
            $value,
            $valueFormatted,
            $timestamp
        );

        $output = new BufferedOutput();
        $this->subject->setOutput($output);
        $this->subject->handleEvent($event);

        $this->assertEquals('#42: mockType (mockName): 10°', trim($output->fetch()));
    }

    public function testHandleEventWithDefaultInterval()
    {
        $sensorVo = new SensorVO();
        $sensorVo->sensorId = 42;
        $sensorVo->type = 'mockType';
        $sensorVo->name = 'mockName';

        $value = 10;
        $valueFormatted = '10°';
        $timestamp = 1000;

        $event = new SensorValueEvent(
            SensorValueEvent::VALUE,
            $sensorVo,
            $value,
            $valueFormatted,
            $timestamp
        );

        $output = new BufferedOutput();
        $this->subject->setOutput($output);
        $this->subject->handleEvent($event);

        $this->assertEquals('#42: mockType (mockName): 10°', trim($output->fetch()));
    }

    public function testHandleErrorEvent()
    {
        $sensorVo = new SensorVO();
        $sensorVo->sensorId = 42;
        $sensorVo->type = 'mockType';
        $sensorVo->name = 'mockName';

        $value = 10;
        $valueFormatted = '10°';
        $timestamp = 1000;

        $event = new SensorValueEvent(
            SensorValueEvent::VALUE,
            $sensorVo,
            $value,
            $valueFormatted,
            $timestamp
        );

        $output = new BufferedOutput();
        $this->subject->setOutput($output);
        $this->subject->handleErrorEvent($event);

        $this->assertEquals('#42: Error while fetching value of sensor mockType (mockName)', trim($output->fetch()));
    }
}
