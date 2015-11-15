<?php

namespace Tests\Homie\Sensors;

use BrainExe\Core\Authentication\Settings\Settings;
use Homie\Sensors\Controller\Controller;
use Homie\Sensors\GetValue\Event;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\Formatter\Formatter;
use Homie\Sensors\SensorVO;
use Homie\Sensors\Builder;
use Symfony\Component\HttpFoundation\Request;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorValuesGateway;
use Homie\Sensors\Chart;
use Homie\Sensors\SensorBuilder;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @covers Homie\Sensors\Controller\Controller
 */
class ControllerTest extends TestCase
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

    /**
     * @var Settings|MockObject
     */
    private $settings;

    public function setUp()
    {
        $this->gateway       = $this->getMock(SensorGateway::class, [], [], '', false);
        $this->valuesGateway = $this->getMock(SensorValuesGateway::class, [], [], '', false);
        $this->chart         = $this->getMock(Chart::class, [], [], '', false);
        $this->builder       = $this->getMock(SensorBuilder::class, [], [], '', false);
        $this->dispatcher    = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->voBuilder     = $this->getMock(Builder::class, [], [], '', false);
        $this->settings      = $this->getMock(Settings::class, [], [], '', false);

        $this->subject = new Controller(
            $this->gateway,
            $this->valuesGateway,
            $this->chart,
            $this->builder,
            $this->voBuilder,
            $this->settings
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

        $sensorsRaw = [
            [
                'sensorId'  => $sensorId = 12,
                'lastValue' => $lastValue,
                'type'      => $type,
            ]
        ];

        $formatter = $this->getMock(Formatter::class);

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
            'activeSensorIds' => $sensorIds,
            'json' => $json,
            'currentFrom' => $from,
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testIndexSensorWithoutFromAndLastValue()
    {
        $from        = null;
        $lastValue   = null;
        $type        = 'sensor_type';

        $request = new Request();
        $request->query->set('from', $from);

        $sensorsRaw = [
            [
                'sensorId' => $sensorId = 12,
                'lastValue' => $lastValue,
                'type' => $type,
            ]
        ];

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
            'sensors'     => $sensorsRaw,
            'activeSensorIds' => [13],
            'json'        => $json,
            'currentFrom' => Chart::DEFAULT_TIME,
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

        $actual = $this->subject->addSensor($request);

        $this->assertEquals($sensorVo, $actual);
    }

    public function testEdit()
    {
        $sensorId = 12;

        $request = new Request();

        $sensorRaw = ['raw'];
        $sensorVo = new SensorVO();
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

        $expected = new SensorVO();
        $this->gateway
            ->expects($this->once())
            ->method('save')
            ->with($expected);

        $this->subject->edit($request, $sensorId);
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

        $event = new Event($sensorVo);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $actual = $this->subject->forceGetValue($request, $sensorId);

        $this->assertTrue($actual);
    }

    public function testSensors()
    {
        $sensors = [0 => ['sensor']];
        $types   = ['sensorsBuilder'];

        $this->voBuilder
            ->expects($this->once())
            ->method('buildFromArray')
            ->with($sensors[0])
            ->willReturn($sensors[0]);

        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensors);
        $this->builder
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($types);

        $actualResult = $this->subject->sensors();

        $expectedValue = [
            'types'   => $types,
            'sensors' => $sensors,
            'fromIntervals' => [
                3600        => _('Last hour'),
                10800       => _('Last 3 hours'),
                86400       => _('Last day'),
                86400 * 7   => _('Last week'),
                86400 * 30  => _('Last month'),
                -1          => _('All time'),
            ]
        ];

        $this->assertEquals($expectedValue, $actualResult);
    }

    public function testDelete()
    {
        $sensorId = 12;
        $request = new Request();

        $this->gateway
            ->expects($this->once())
            ->method('deleteSensor')
            ->willReturn($sensorId);

        $actualResult = $this->subject->delete($request, $sensorId);

        $this->assertTrue($actualResult);
    }
}
