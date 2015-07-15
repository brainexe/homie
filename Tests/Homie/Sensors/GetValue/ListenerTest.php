<?php

namespace Tests\Homie\Sensors\GetValue;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\Time;
use Homie\Sensors\Formatter\Formatter;
use Homie\Sensors\GetValue\Listener;
use Homie\Sensors\Builder;
use Homie\Sensors\GetValue\Event;
use Homie\Sensors\Interfaces\Sensor;
use Homie\Sensors\SensorBuilder;
use Homie\Sensors\SensorValueEvent;
use Homie\Sensors\SensorValuesGateway;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

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
     * @var SensorValuesGateway
     */
    private $valuesGateway;

    public function setUp()
    {
        $this->valuesGateway = $this->getMock(SensorValuesGateway::class, [], [], '', false);
        $this->builder       = $this->getMock(SensorBuilder::class, [], [], '', false);
        $this->voBuilder     = $this->getMock(Builder::class, [], [], '', false);
        $this->dispatcher    = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->time          = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new Listener(
            $this->builder,
            $this->voBuilder,
            $this->valuesGateway
        );

        $this->subject->setEventDispatcher($this->dispatcher);
        $this->subject->setTime($this->time);
    }

    public function testGetSubscribedEvents()
    {
        $actual = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actual);
    }

    public function testHandleWithFailedResult()
    {
        $sensorVo = new SensorVO();
        $sensorVo->type = 'mockType';
        $sensor = $this->getMock(Sensor::class);

        $this->builder
            ->expects($this->once())
            ->method('build')
            ->with('mockType')
            ->willReturn($sensor);

        $event = new Event($sensorVo);

        $sensor
            ->expects($this->once())
            ->method('getValue')
            ->willReturn(null);

        $this->subject->handle($event);
    }

    public function testHandleWith()
    {
        $sensorVo = new SensorVO();
        $sensorVo->type = 'mockType';

        /** @var Sensor|MockObject $sensor */
        $sensor = $this->getMock(Sensor::class);
        $formatter = $this->getMock(Formatter::class);

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
            ->with('mockType')
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
            $sensor,
            $value,
            '42°',
            $now
        );
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($expectedEvent);

        $event = new Event($sensorVo);
        $this->subject->handle($event);
    }
}
