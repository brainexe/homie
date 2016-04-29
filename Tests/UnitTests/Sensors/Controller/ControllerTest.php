<?php

namespace Tests\Homie\Sensors\Controller;

use ArrayIterator;
use BrainExe\Core\Authentication\Settings\Settings;
use BrainExe\Core\Util\Time;
use Homie\Sensors\Controller\Controller;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\SensorVO;
use Homie\Sensors\Builder;
use Symfony\Component\HttpFoundation\Request;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorValuesGateway;
use Homie\Sensors\Chart;
use Homie\Sensors\SensorBuilder;

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
     * @var Builder|MockObject
     */
    private $voBuilder;

    /**
     * @var Settings|MockObject
     */
    private $settings;

    /**
     * @var Time|MockObject
     */
    private $time;

    public function setUp()
    {
        $this->gateway       = $this->getMock(SensorGateway::class, [], [], '', false);
        $this->valuesGateway = $this->getMock(SensorValuesGateway::class, [], [], '', false);
        $this->chart         = $this->getMock(Chart::class, [], [], '', false);
        $this->builder       = $this->getMock(SensorBuilder::class, [], [], '', false);
        $this->voBuilder     = $this->getMock(Builder::class, [], [], '', false);
        $this->settings      = $this->getMock(Settings::class, [], [], '', false);
        $this->time          = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new Controller(
            $this->gateway,
            $this->valuesGateway,
            $this->chart,
            $this->builder,
            $this->voBuilder,
            $this->settings
        );

        $this->subject->setTime($this->time);
    }

    public function testIndexSensor()
    {
        $from             = 10;
        $now              = 1000;
        $activeSensorIds  = null;
        $lastValue        = 100;
        $type             = 'sensor_type';

        $request = new Request();
        $request->query->set('from', $from);
        $request->query->set('save', 1);
        $request->attributes->set('user_id', $userId = 42);

        $sensorsRaw = [
            [
                'sensorId'  => $sensorId = 12,
                'lastValue' => $lastValue,
                'type'      => $type,
                'formatter' => 'formatter'
            ]
        ];

        $sensorIds = [$sensorId];
        $this->gateway
            ->expects($this->once())
            ->method('getSensorIds')
            ->willReturn($sensorIds);

        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensorsRaw);

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $sensorValues = ['values'];

        $this->valuesGateway
            ->expects($this->once())
            ->method('getSensorValues')
            ->with($sensorId, 990, $now)
            ->willReturn($sensorValues);

        $this->settings
            ->expects($this->at(0))
            ->method('get')
            ->with($userId, Controller::SETTINGS_ACTIVE_SENSORS);

        $this->settings
            ->expects($this->at(1))
            ->method('set')
            ->with($userId, Controller::SETTINGS_ACTIVE_SENSORS, '12');

        $this->settings
            ->expects($this->at(2))
            ->method('set')
            ->with($userId, Controller::SETTINGS_TIMESPAN, '10');

        $json = ['json'];
        $this->chart
            ->expects($this->once())
            ->method('formatJsonData')
            ->with($sensorsRaw, [$sensorId => $sensorValues])
            ->willReturn(new ArrayIterator($json));

        $actual = $this->subject->indexSensor($request, $activeSensorIds);

        $expected = [
            'json' => $json,
            'from' => $from,
            'to' => $now,
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testIndexSensorWithoutFromAndLastValue()
    {
        $from        = null;
        $lastValue   = null;
        $now         = 1000;
        $type        = 'sensor_type';

        $request = new Request();
        $request->query->set('from', $from);

        $sensorsRaw = [
            [
                'sensorId'  => $sensorId = 12,
                'lastValue' => $lastValue,
                'type'      => $type,
            ]
        ];

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->with([12])
            ->willReturn($sensorsRaw);

        $json = ['json'];
        $this->chart
            ->expects($this->once())
            ->method('formatJsonData')
            ->with($sensorsRaw, [$sensorId => []])
            ->willReturn(new ArrayIterator($json));

        $actual = $this->subject->indexSensor($request, "12");

        $expected = [
            'json' => $json,
            'from' => Chart::DEFAULT_TIME,
            'to'   => $now,
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testSensors()
    {
        $sensorRaw = [0 => ['sensor']];
        $sensors = [0 => new SensorVO()];
        $types   = ['sensorsBuilder'];

        $this->voBuilder
            ->expects($this->once())
            ->method('buildFromArray')
            ->with($sensorRaw[0])
            ->willReturn($sensors[0]);

        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensorRaw);
        $this->builder
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($types);
        $this->builder
            ->expects($this->once())
            ->method('getFormatters')
            ->willReturn(['formatter']);

        $actual = $this->subject->sensors();

        $expected = [
            'types'         => $types,
            'sensors'       => $sensors,
            'formatters'    => ['formatter'],
            'fromIntervals' => Chart::getTimeSpans()
        ];

        $this->assertEquals($expected, $actual);
    }
}
