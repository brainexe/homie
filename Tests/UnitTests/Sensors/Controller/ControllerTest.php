<?php

namespace Tests\Homie\Sensors\Controller;

use ArrayIterator;
use BrainExe\Core\Authentication\Settings\Settings;
use BrainExe\Core\Util\Time;
use Homie\Sensors\Controller\Controller;
use Homie\Sensors\SensorVO;
use Homie\Sensors\Builder;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorValuesGateway;
use Homie\Sensors\Chart;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpFoundation\Request;

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
        $this->gateway       = $this->createMock(SensorGateway::class);
        $this->valuesGateway = $this->createMock(SensorValuesGateway::class);
        $this->chart         = $this->createMock(Chart::class);
        $this->voBuilder     = $this->createMock(Builder::class);
        $this->settings      = $this->createMock(Settings::class);
        $this->time          = $this->createMock(Time::class);

        $this->subject = new Controller(
            $this->gateway,
            $this->valuesGateway,
            $this->chart,
            $this->voBuilder,
            $this->settings
        );

        $this->subject->setTime($this->time);
    }

    public function testIndexSensor()
    {
        $ago              = 10;
        $now              = 1000;
        $activeSensorIds  = '';
        $lastValue        = 100;
        $type             = 'sensor_type';

        $request = new Request();
        $request->query->set('from', $ago);
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
            ->with($userId, Controller::SETTINGS_ACTIVE_SENSORS)
            ->willReturn([]);

        $this->settings
            ->expects($this->at(1))
            ->method('set')
            ->with($userId, Controller::SETTINGS_ACTIVE_SENSORS, [12]);

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
            'from' => $now - $ago,
            'ago'  => $ago,
            'to'   => $now,
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
            'ago'  => Chart::DEFAULT_TIME,
            'from' => $now - Chart::DEFAULT_TIME,
            'to'   => $now,
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testSensors()
    {
        $sensorRaw = [0 => ['sensor']];
        $sensors = [0 => new SensorVO()];

        $this->voBuilder
            ->expects($this->once())
            ->method('buildFromArray')
            ->with($sensorRaw[0])
            ->willReturn($sensors[0]);

        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensorRaw);

        $actual = $this->subject->sensors();

        $this->assertInternalType('array', $actual['types']);
        $this->assertInternalType('array', $actual['formatters']);
        $this->assertEquals($sensors, $actual['sensors']);
        $this->assertEquals(Chart::getTimeSpans(), $actual['fromIntervals']);
    }
}
