<?php

namespace Tests\Homie\Sensors;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Homie\Sensors\Controller;
use Homie\Sensors\Formatter\Formatter;
use Homie\Sensors\Interfaces\Sensor;
use Homie\Sensors\SensorVO;
use Homie\Sensors\Builder;
use Symfony\Component\HttpFoundation\Request;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorValuesGateway;
use Homie\Sensors\Chart;
use Homie\Sensors\SensorBuilder;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @covers Homie\Sensors\Controller
 */
class ControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Controller
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
     * @var Chart|MockObject
     */
    private $chart;

    /**
     * @var SensorBuilder|MockObject
     */
    private $builder;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    /**
     * @var Builder|MockObject
     */
    private $voBuilder;

    public function setUp()
    {
        $this->gateway       = $this->getMock(SensorGateway::class, [], [], '', false);
        $this->valuesGateway = $this->getMock(SensorValuesGateway::class, [], [], '', false);
        $this->chart         = $this->getMock(Chart::class, [], [], '', false);
        $this->builder       = $this->getMock(SensorBuilder::class, [], [], '', false);
        $this->dispatcher    = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->voBuilder     = $this->getMock(Builder::class, [], [], '', false);

        $this->subject = new Controller(
            $this->gateway,
            $this->valuesGateway,
            $this->chart,
            $this->builder,
            $this->voBuilder
        );
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testIndexSensor()
    {
        $from             = 10;
        $activeSensorIds  = null;
        $lastValue        = 100;
        $formattedValue   = '100 grad';
        $type             = 'sensor_type';

        $request = new Request();
        $request->query->set('from', $from);

        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $sensorsRaw = [
            [
                'sensorId'  => $sensorId = 12,
                'lastValue' => $lastValue,
                'type'      => $type,
            ]
        ];

        $sensorsObj = [
            $type => $this->getMock(Sensor::class)
        ];

        $formatter = $this->getMock(Formatter::class);

        $this->builder
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensorsObj);

        $this->builder
            ->expects($this->once())
            ->method('getFormatter')
            ->with($type)
            ->willReturn($formatter);

        $sensorIds = [$sensorId];
        $this->gateway
            ->expects($this->once())
            ->method('getSensorIds')
            ->willReturn($sensorIds);

        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensorsRaw);

        $formatter
            ->expects($this->once())
            ->method('formatValue')
            ->with($lastValue)
            ->willReturn($formattedValue);

        $sensorValues = ['values'];
        $sensorsRaw[0]['lastValue'] = $formattedValue;

        $this->valuesGateway
            ->expects($this->once())
            ->method('getSensorValues')
            ->with($sensorId, $from)
            ->willReturn($sensorValues);

        $json = ['json'];
        $this->chart
            ->expects($this->once())
            ->method('formatJsonData')
            ->with($sensorsRaw, [$sensorId => $sensorValues])
            ->willReturn($json);

        $actualResult = $this->subject->indexSensor($request, $activeSensorIds);

        $expectedResult = [
            'sensors' => $sensorsRaw,
            'active_sensor_ids' => $sensorIds,
            'json' => $json,
            'current_from' => $from,
            'available_sensors' => $sensorsObj,
            'fromIntervals' => [
                0 => 'All',
                3600 => 'Last Hour',
                86400 => 'Last Day',
                86400 * 7 => 'Last Week',
                86400 * 30 => 'Last Month'
            ]
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testIndexSensorWithoutFromAndLastValue()
    {
        $from              = null;
        $lastValue         = null;
        $type              = 'sensor_type';

        $request = new Request();
        $request->query->set('from', $from);

        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $sensorsRaw = [
            [
                'sensorId' => $sensorId = 12,
                'lastValue' => $lastValue,
                'type' => $type,
            ]
        ];

        $sensorsObj = [
            $type => $this->getMock(Sensor::class)
        ];

        $this->builder
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensorsObj);

        $sensorIds = [$sensorId];
        $this->gateway
            ->expects($this->once())
            ->method('getSensorIds')
            ->willReturn($sensorIds);

        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensorsRaw);

        $json = ['json'];
        $this->chart
            ->expects($this->once())
            ->method('formatJsonData')
            ->with($sensorsRaw, [])
            ->willReturn($json);

        $actualResult = $this->subject->indexSensor($request, "13");

        $expectedResult = [
            'sensors' => $sensorsRaw,
            'active_sensor_ids' => [13],
            'json' => $json,
            'current_from' => Chart::DEFAULT_TIME,
            'available_sensors' => $sensorsObj,
            'fromIntervals' => [
                0 => 'All',
                3600 => 'Last Hour',
                86400 => 'Last Day',
                86400 * 7 => 'Last Week',
                86400 * 30 => 'Last Month'
            ]
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAddSensor()
    {
        $type        = 'type';
        $name        = 'name';
        $description = 'descritpion';
        $pin         = 'pin';
        $interval    = 12;
        $node        = 1;

        $request = new Request();
        $request->request->set('type', $type);
        $request->request->set('name', $name);
        $request->request->set('description', $description);
        $request->request->set('pin', $pin);
        $request->request->set('interval', $interval);
        $request->request->set('node', $node);

        $sensorVo = new SensorVO();
        $sensorVo->name = $name;

        $this->voBuilder
            ->expects($this->once())
            ->method('build')
            ->with(
                null,
                $name,
                $description,
                $interval,
                $node,
                $pin,
                $type
            )
            ->willReturn($sensorVo);
        $this->gateway
            ->expects($this->once())
            ->method('addSensor')
            ->with($sensorVo);

        $actualResult = $this->subject->addSensor($request);

        $this->assertEquals($sensorVo, $actualResult);
    }

    public function testEspeak()
    {
        $request = new Request();
        $sensorId = 10;
        $espeakText = 'last value';

        $sensor = [
            'type' => $sensorType = 'type',
            'lastValue' => 'last value',
        ];

        $this->gateway
            ->expects($this->once())
            ->method('getSensor')
            ->with($sensorId)
            ->willReturn($sensor);

        $formatter = $this->getMock(Formatter::class);

        $this->builder
            ->expects($this->once())
            ->method('getFormatter')
            ->with($sensorType)
            ->willReturn($formatter);

        $formatter
            ->expects($this->once())
            ->method('getEspeakText')
            ->willReturn($espeakText);

        $espeakVo = new EspeakVO($espeakText);
        $event    = new EspeakEvent($espeakVo);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $actualResult = $this->subject->espeak($request, $sensorId);

        $this->assertTrue($actualResult);
    }

    public function testSlim()
    {
        $sensorId             = 12;
        $sensorValueFormatted = 100;
        $sensorValue          = '100 grad';
        $type                 = 'sensor type';

        $request = new Request();

        $sensorRaw = [
            'type'       => $type,
            'lastValue'  => $sensorValue
        ];

        $formatter = $this->getMock(Formatter::class);

        $this->gateway
            ->expects($this->once())
            ->method('getSensor')
            ->with($sensorId)
            ->willReturn($sensorRaw);

        $this->builder
            ->expects($this->once())
            ->method('getFormatter')
            ->with($type)
            ->willReturn($formatter);

        $formatter
            ->expects($this->once())
            ->method('getEspeakText')
            ->with($sensorValue)
            ->willReturn($sensorValueFormatted);

        $actualResult = $this->subject->slim($request, $sensorId);

        $expectedValue = [
            'sensor' => $sensorRaw,
            'sensor_value_formatted' => $sensorValueFormatted,
            'refresh_interval' => 60
        ];

        $this->assertEquals($expectedValue, $actualResult);
    }

    public function testGetValue()
    {
        $sensorId             = 12;
        $sensorValueFormatted = 100;
        $sensorValue          = '100 grad';
        $type                 = 'sensor type';

        $request = new Request();
        $request->query->set('sensor_id', $sensorId);

        $sensorRaw = [
            'type'       => $type,
            'lastValue'  => $sensorValue
        ];

        $formatter    = $this->getMock(Formatter::class);
        $sensorObject = $this->getMock(Sensor::class);

        $this->gateway
            ->expects($this->once())
            ->method('getSensor')
            ->with($sensorId)
            ->willReturn($sensorRaw);

        $this->builder
            ->expects($this->once())
            ->method('getFormatter')
            ->with($type)
            ->willReturn($formatter);
        $this->builder
            ->expects($this->once())
            ->method('build')
            ->with($type)
            ->willReturn($sensorObject);

        $formatter
            ->expects($this->once())
            ->method('getEspeakText')
            ->with($sensorValue)
            ->willReturn($sensorValueFormatted);

        $actualResult = $this->subject->getValue($request);

        $expectedValue = [
            'sensor' => $sensorRaw,
            'sensor_value_formatted' => $sensorValueFormatted,
            'refresh_interval' => 60,
            'sensor_obj' => $sensorObject
        ];

        $this->assertEquals($expectedValue, $actualResult);
    }

    public function testApi()
    {
        $request = new Request();

        $sensors = [
            [
                'type'       => 'mockType',
                'sensorId'   => 'mockSensorId',
                'lastValue'  => 'mockValue'
            ]
        ];

        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensors);

        $actualResult = $this->subject->api($request);

        $expectedValue = [
            'mockType_mockSensorId' => 'mockValue'
        ];

        $this->assertEquals($expectedValue, $actualResult);
    }
}
