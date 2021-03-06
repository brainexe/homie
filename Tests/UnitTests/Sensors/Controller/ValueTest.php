<?php

namespace Tests\Homie\Sensors\Controller;

use ArrayIterator;
use BrainExe\Core\Util\Time;
use Homie\Sensors\Controller\Values;
use Homie\Sensors\GetValue\GetSensorValueEvent;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\SensorVO;
use Homie\Sensors\Builder;
use Symfony\Component\HttpFoundation\Request;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorValuesGateway;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @covers \Homie\Sensors\Controller\Values
 */
class ValuesTest extends TestCase
{

    /**
     * @var Values
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
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    /**
     * @var Time|MockObject
     */
    private $time;

    /**
     * @var Builder|MockObject
     */
    private $voBuilder;

    public function setUp()
    {
        $this->gateway       = $this->createMock(SensorGateway::class);
        $this->valuesGateway = $this->createMock(SensorValuesGateway::class);
        $this->voBuilder     = $this->createMock(Builder::class);
        $this->dispatcher    = $this->createMock(EventDispatcher::class);
        $this->time          = $this->createMock(Time::class);

        $this->subject = new Values(
            $this->gateway,
            $this->valuesGateway,
            $this->voBuilder
        );
        $this->subject->setEventDispatcher($this->dispatcher);
        $this->subject->setTime($this->time);

    }

    public function testAddValue()
    {
        $sensorId = 12;
        $value    = 42;
        $request  = new Request();
        $request->request->set('value', $value);

        $sensorRaw = ['raw'];
        $sensorVo  = new SensorVO();
        $this->gateway
            ->expects($this->once())
            ->method('getSensor')
            ->with($sensorId)
            ->willReturn($sensorRaw);
        $this->voBuilder
            ->expects($this->once())
            ->method('buildFromArray')
            ->with($sensorRaw)
            ->willReturn($sensorVo);

        $this->valuesGateway
            ->expects($this->once())
            ->method('addValue')
            ->with($sensorVo, $value);

        $actual = $this->subject->addValue($request, $sensorId);

        $this->assertTrue($actual);
    }

    public function testForceGetValue()
    {
        $sensorId = 12;
        $value    = 42;
        $request  = new Request();
        $request->request->set('value', $value);

        $sensorRaw = ['raw'];
        $sensorVo  = new SensorVO();
        $this->gateway
            ->expects($this->once())
            ->method('getSensor')
            ->with($sensorId)
            ->willReturn($sensorRaw);
        $this->voBuilder
            ->expects($this->once())
            ->method('buildFromArray')
            ->with($sensorRaw)
            ->willReturn($sensorVo);

        $event = new GetSensorValueEvent($sensorVo);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $actual = $this->subject->forceGetValue($request, $sensorId);

        $this->assertTrue($actual);
    }

    public function testGetByTime()
    {
        $request = new Request();
        $request->query->set('timestamp', $time = 122323);
        $request->query->set('sensorIds', '12,13');

        $iterator = new ArrayIterator(['test']);
        $this->valuesGateway
            ->expects($this->once())
            ->method('getByTime')
            ->with(['12', '13'], $time)
            ->willReturn($iterator);

        $actual = $this->subject->getByTime($request);

        $this->assertEquals($iterator, $actual);
    }

    public function testDeleteValues()
    {
        $request = new Request();

        $sensorId  = 42;
        $timestamp = 12121;

        $this->valuesGateway
            ->expects($this->once())
            ->method('deleteValue')
            ->with($sensorId, $timestamp)
            ->willReturn(true);

        $actual = $this->subject->delete($request, $sensorId, $timestamp);

        $this->assertEquals(true, $actual);
    }

    public function testGetByTimeWithoutTimeShouldReturnCurrentValue()
    {
        $request = new Request();
        $request->query->set('timestamp', 0);
        $request->query->set('sensorIds', '12,13');

        $now = 1000;
        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $iterator = new ArrayIterator(['test']);
        $this->valuesGateway
            ->expects($this->once())
            ->method('getByTime')
            ->with(['12', '13'], $now)
            ->willReturn($iterator);

        $actual = $this->subject->getByTime($request);

        $this->assertEquals($iterator, $actual);
    }

    public function testGetValue()
    {
        $sensorId    = 12;
        $sensorValue = '100 grad';
        $type        = 'sensor type';

        $request = new Request();

        $sensorRaw = [
            'type'       => $type,
            'lastValue'  => $sensorValue
        ];

        $this->gateway
            ->expects($this->once())
            ->method('getSensor')
            ->with($sensorId)
            ->willReturn($sensorRaw);

        $actual = $this->subject->getValue($request, $sensorId);

        $expected = $sensorRaw;

        $this->assertEquals($expected, $actual);
    }
}
