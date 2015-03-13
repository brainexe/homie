<?php

namespace Tests\Raspberry\Sensors\Command;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\Command\Cron;
use Raspberry\Sensors\Definition;
use Raspberry\Sensors\Formatter\Formatter;
use Raspberry\Sensors\Interfaces\Sensor;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValueEvent;
use Raspberry\Sensors\SensorValuesGateway;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorVO;
use Raspberry\Sensors\Builder;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Monolog\Logger;
use BrainExe\Core\Util\Time;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers Raspberry\Sensors\Command\Cron
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
     * @var SensorValuesGateway|MockObject
     */
    private $valuesGateway;

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
     * @var Logger|MockObject
     */
    private $logger;

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
        $this->valuesGateway = $this->getMock(SensorValuesGateway::class, [], [], '', false);
        $this->builder       = $this->getMock(SensorBuilder::class, [], [], '', false);
        $this->voBuilder     = $this->getMock(Builder::class, [], [], '', false);
        $this->dispatcher    = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->logger        = $this->getMock(Logger::class, [], [], '', false);
        $this->mockTime      = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new Cron(
            $this->gateway,
            $this->valuesGateway,
            $this->builder,
            $this->voBuilder,
            $this->dispatcher,
            $this->nodeId
        );
        $this->subject->setLogger($this->logger);
        $this->subject->setTime($this->mockTime);
    }

    public function testExecuteWithEmptyValue()
    {
        $application = new Application();
        $application->setAutoExit(false);
        $application->add($this->subject);

        $now = 1000;
        $sensors = [
            $sensorRaw = []
        ];

        $sensor = new SensorVO();
        $sensor->sensorId = 10;
        $sensor->type = $type = 'type';
        $sensor->pin = $pin = 12;
        $sensor->name = 'name';

        $currentSensorValue = null;

        $formatter  = $this->getMock(Formatter::class);

        $definition = new Definition();
        $definition->name = 'type';

        $sensorObject = $this->getMockForAbstractClass(Sensor::class);
        $this->mockTime
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->with($this->nodeId)
            ->willReturn($sensors);

        $this->voBuilder
            ->expects($this->once())
            ->method('buildFromArray')
            ->with($sensorRaw)
            ->willReturn($sensor);

        $this->builder
            ->expects($this->once())
            ->method('build')
            ->with($type)
            ->willReturn($sensorObject);

        $this->builder
            ->expects($this->once())
            ->method('getFormatter')
            ->with($type)
            ->willReturn($formatter);

        $this->builder
            ->expects($this->once())
            ->method('getDefinition')
            ->with($type)
            ->willReturn($definition);

        $sensorObject
            ->expects($this->once())
            ->method('getValue')
            ->with($pin)
            ->willReturn($currentSensorValue);

        $input = ['--force'];
        $commandTester = new CommandTester($this->subject);
        $commandTester->execute($input);

        $output = $commandTester->getDisplay();
        $this->assertEquals("Invalid sensor value: #10 type (name)\n", $output);
    }

    public function testExecute()
    {
        $now = 1000;
        $sensors = [
            $sensorRaw = []
        ];

        $sensor = new SensorVO();
        $sensor->sensorId = 10;
        $sensor->type = $type = 'type';
        $sensor->pin = $pin = 12;
        $sensor->name = 'name';

        $currentSensorValue = 1000;
        $formattedSensorValue = "1000 grad";

        $formatter = $this->getMock(Formatter::class);

        $definition = new Definition();
        $definition->name = 'type';

        $formatter
            ->expects($this->once())
            ->method('formatValue')
            ->with($currentSensorValue)
            ->willReturn($formattedSensorValue);

        /** @var Sensor|MockObject $sensorObject */
        $sensorObject = $this->getMockForAbstractClass(Sensor::class);
        $this->mockTime
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $this->builder
            ->expects($this->once())
            ->method('getFormatter')
            ->with($type)
            ->willReturn($formatter);

        $this->builder
            ->expects($this->once())
            ->method('getDefinition')
            ->with($type)
            ->willReturn($definition);

        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->with($this->nodeId)
            ->willReturn($sensors);

        $this->voBuilder
            ->expects($this->once())
            ->method('buildFromArray')
            ->with($sensorRaw)
            ->willReturn($sensor);

        $this->builder
            ->expects($this->once())
            ->method('build')
            ->with($type)
            ->willReturn($sensorObject);

        $sensorObject
            ->expects($this->once())
            ->method('getValue')
            ->with($pin)
            ->willReturn($currentSensorValue);

        $event = new SensorValueEvent(
            $sensor,
            $sensorObject,
            $currentSensorValue,
            $formattedSensorValue,
            $now
        );

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $application = new Application();
        $application->add($this->subject);
        $input = ['--force'];
        $commandTester = new CommandTester($this->subject);
        $commandTester->execute($input);

        $output = $commandTester->getDisplay();
        $this->assertEquals("#10: type (name): 1000 grad\n", $output);
    }
}
