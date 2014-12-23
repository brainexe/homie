<?php

namespace Tests\Raspberry\Console\SensorCronCommand;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Console\SensorCronCommand;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\Sensors\SensorInterface;
use Raspberry\Sensors\SensorValueEvent;
use Raspberry\Sensors\SensorValuesGateway;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorVO;
use Raspberry\Sensors\SensorVOBuilder;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Monolog\Logger;
use BrainExe\Core\Util\Time;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers Raspberry\Console\SensorCronCommand
 */
class SensorCronCommandTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SensorCronCommand
     */
    private $subject;

    /**
     * @var SensorGateway|MockObject
     */
    private $mockSensorGateway;

    /**
     * @var SensorValuesGateway|MockObject
     */
    private $mockSensorValuesGateway;

    /**
     * @var SensorBuilder|MockObject
     */
    private $mockSensorBuilder;

    /**
     * @var SensorVOBuilder|MockObject
     */
    private $mockSensorVOBuilder;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    /**
     * @var Logger|MockObject
     */
    private $mockLogger;

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
        $this->mockSensorGateway = $this->getMock(SensorGateway::class, [], [], '', false);
        $this->mockSensorValuesGateway = $this->getMock(SensorValuesGateway::class, [], [], '', false);
        $this->mockSensorBuilder = $this->getMock(SensorBuilder::class, [], [], '', false);
        $this->mockSensorVOBuilder = $this->getMock(SensorVOBuilder::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->mockLogger = $this->getMock(Logger::class, [], [], '', false);
        $this->mockTime = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new SensorCronCommand(
            $this->mockSensorGateway,
            $this->mockSensorValuesGateway,
            $this->mockSensorBuilder,
            $this->mockSensorVOBuilder,
            $this->mockEventDispatcher,
            $this->nodeId
        );
        $this->subject->setLogger($this->mockLogger);
        $this->subject->setTime($this->mockTime);
    }

    public function testExecuteWithEmptyValue()
    {
        $application = new Application();
        $application->setAutoExit(false);
        $application->add($this->subject);

        $now = 1000;
        $sensors = [
        $sensor_raw = [
        ]
        ];

        $sensor = new SensorVO();
        $sensor->sensorId = 10;
        $sensor->type = $type = 'type';
        $sensor->pin = $pin = 12;
        $sensor->name = $name = 'name';

        $current_sensor_value = null;

        $sensor_object = $this->getMockForAbstractClass(SensorInterface::class);
        $this->mockTime
        ->expects($this->once())
        ->method('now')
        ->willReturn($now);

        $this->mockSensorGateway
        ->expects($this->once())
        ->method('getSensors')
        ->with($this->nodeId)
        ->willReturn($sensors);

        $this->mockSensorVOBuilder
        ->expects($this->once())
        ->method('buildFromArray')
        ->with($sensor_raw)
        ->willReturn($sensor);

        $this->mockSensorBuilder
        ->expects($this->once())
        ->method('build')
        ->with($type)
        ->willReturn($sensor_object);

        $sensor_object
        ->expects($this->once())
        ->method('getValue')
        ->with($pin)
        ->willReturn($current_sensor_value);

        $input = ['--force'];
        $command_tester = new CommandTester($this->subject);
        $command_tester->execute($input);

        $output = $command_tester->getDisplay();
        $this->assertEquals("Invalid sensor value: #10 type (name)\n", $output);
    }
    public function testExecute()
    {
        $now = 1000;
        $sensors = [
        $sensor_raw = [
        ]
        ];

        $sensor = new SensorVO();
        $sensor->sensorId = 10;
        $sensor->type = $type = 'type';
        $sensor->pin = $pin = 12;
        $sensor->name = $name = 'name';

        $current_sensor_value = 1000;
        $formatted_sensor_value = "1000 grad";

        /** @var SensorInterface|MockObject $sensor_object */
        $sensor_object = $this->getMockForAbstractClass(SensorInterface::class);
        $this->mockTime
        ->expects($this->once())
        ->method('now')
        ->willReturn($now);

        $this->mockSensorGateway
        ->expects($this->once())
        ->method('getSensors')
        ->with($this->nodeId)
        ->willReturn($sensors);

        $this->mockSensorVOBuilder
        ->expects($this->once())
        ->method('buildFromArray')
        ->with($sensor_raw)
        ->willReturn($sensor);

        $this->mockSensorBuilder
        ->expects($this->once())
        ->method('build')
        ->with($type)
        ->willReturn($sensor_object);

        $sensor_object
        ->expects($this->once())
        ->method('getValue')
        ->with($pin)
        ->willReturn($current_sensor_value);

        $sensor_object
        ->expects($this->once())
        ->method('formatValue')
        ->with($current_sensor_value)
        ->willReturn($formatted_sensor_value);

        $event = new SensorValueEvent(
            $sensor,
            $sensor_object,
            $current_sensor_value,
            $formatted_sensor_value,
            $now
        );

        $this->mockEventDispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $application = new Application();
        $application->add($this->subject);
        $input = ['--force'];
        $command_tester = new CommandTester($this->subject);
        $command_tester->execute($input);

        $output = $command_tester->getDisplay();
        $this->assertEquals("Sensor value: #10 type (name): 1000 grad\n", $output);
    }
}
