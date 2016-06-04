<?php

namespace Tests\Homie\Sensors\GetValue;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\Time;
use Exception;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\Formatter\Formatter;
use Homie\Sensors\GetValue\Listener;
use Homie\Sensors\GetValue\GetSensorValueEvent;
use Homie\Sensors\Interfaces\Sensor;
use Homie\Sensors\SensorBuilder;
use Homie\Sensors\SensorValueEvent;
use Homie\Sensors\SensorValuesGateway;
use Homie\Sensors\SensorVO;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Psr\Log\LogLevel;

class ListenerTest extends TestCase
{

    /**
     * @var Listener
     */
    private $subject;

    /**
     * @var SensorBuilder|MockObject
     */
    private $builder;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    /**
     * @var Time|MockObject
     */
    private $time;

    /**
     * @var SensorValuesGateway|MockObject
     */
    private $valuesGateway;

    /**
     * @var Logger|MockObject
     */
    private $logger;

    public function setUp()
    {
        $this->valuesGateway = $this->createMock(SensorValuesGateway::class);
        $this->builder       = $this->createMock(SensorBuilder::class);
        $this->dispatcher    = $this->createMock(EventDispatcher::class);
        $this->time          = $this->createMock(Time::class);
        $this->logger        = $this->createMock(Logger::class);

        $this->subject = new Listener(
            $this->builder,
            $this->valuesGateway
        );

        $this->subject->setEventDispatcher($this->dispatcher);
        $this->subject->setTime($this->time);
        $this->subject->setLogger($this->logger);
    }

    public function testHandleWithFailedResult()
    {
        $sensorVo = new SensorVO();
        $sensorVo->type = 'mockType';
        $sensor = $this->createMock(Sensor::class);

        $this->builder
            ->expects($this->once())
            ->method('build')
            ->with('mockType')
            ->willReturn($sensor);

        $event = new GetSensorValueEvent($sensorVo);

        $sensor
            ->expects($this->once())
            ->method('getValue')
            ->willThrowException(new InvalidSensorValueException($sensorVo, 'invalid value'));

        $this->subject->handle($event);
    }

    public function testHandleWithException()
    {
        $sensorVo = new SensorVO();
        $sensorVo->type = 'mockType';
        $sensor = $this->createMock(Sensor::class);

        $this->builder
            ->expects($this->once())
            ->method('build')
            ->with('mockType')
            ->willReturn($sensor);

        $event = new GetSensorValueEvent($sensorVo);

        $sensor
            ->expects($this->once())
            ->method('getValue')
            ->willThrowException(new InvalidSensorValueException($sensorVo, 'my sensor exception'));

        $this->logger
            ->expects($this->once())
            ->method('log');

        $this->subject->handle($event);
    }

    public function testHandleWith()
    {
        $sensorVo = new SensorVO();
        $sensorVo->type = 'mockType';
        $sensorVo->formatter = 'formatter';

        /** @var Sensor|MockObject $sensor */
        $sensor = $this->createMock(Sensor::class);
        $formatter = $this->createMock(Formatter::class);

        $value = 42;
        $now = 100;

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $this->builder
            ->expects($this->once())
            ->method('build')
            ->with('mockType')
            ->willReturn($sensor);
        $this->builder
            ->expects($this->once())
            ->method('getFormatter')
            ->with('formatter')
            ->willReturn($formatter);

        $sensor
            ->expects($this->once())
            ->method('getValue')
            ->willReturn($value);

        $formatter
            ->expects($this->once())
            ->method('formatValue')
            ->with($value)
            ->willReturn('42°');

        $expectedEvent = new SensorValueEvent(
            SensorValueEvent::VALUE,
            $sensorVo,
            $value,
            '42°',
            $now
        );
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($expectedEvent);
        $this->logger
            ->expects($this->once())
            ->method('log')
            ->with(LogLevel::DEBUG);

        $event = new GetSensorValueEvent($sensorVo);
        $this->subject->handle($event);
    }
}
