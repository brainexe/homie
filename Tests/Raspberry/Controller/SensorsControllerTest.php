<?php

namespace Tests\Raspberry\Controller\SensorsController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Controller\SensorsController;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Raspberry\Sensors\Sensors\SensorInterface;
use Raspberry\Sensors\SensorVO;
use Raspberry\Sensors\SensorVOBuilder;
use Symfony\Component\HttpFoundation\Request;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Raspberry\Sensors\Chart;
use Raspberry\Sensors\SensorBuilder;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @Covers Raspberry\Controller\SensorsController
 */
class SensorsControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SensorsController
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
     * @var Chart|MockObject
     */
    private $mockChart;

    /**
     * @var SensorBuilder|MockObject
     */
    private $mockSensorBuilder;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    /**
     * @var SensorVOBuilder|MockObject
     */
    private $mockVoBuilder;

    public function setUp()
    {
        $this->mockSensorGateway       = $this->getMock(SensorGateway::class, [], [], '', false);
        $this->mockSensorValuesGateway = $this->getMock(SensorValuesGateway::class, [], [], '', false);
        $this->mockChart               = $this->getMock(Chart::class, [], [], '', false);
        $this->mockSensorBuilder       = $this->getMock(SensorBuilder::class, [], [], '', false);
        $this->mockEventDispatcher     = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->mockVoBuilder           = $this->getMock(SensorVOBuilder::class, [], [], '', false);

        $this->subject = new SensorsController(
            $this->mockSensorGateway,
            $this->mockSensorValuesGateway,
            $this->mockChart,
            $this->mockSensorBuilder,
            $this->mockVoBuilder
        );
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
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
                'id' => $sensorId = 12,
                'last_value' => $lastValue,
                'type' => $type,
            ]
        ];

        $sensors_obj = [
            $type => $sensor = $this->getMock(SensorInterface::class)
        ];

        $this->mockSensorBuilder
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensors_obj);

        $sensorIds = [$sensorId];
        $this->mockSensorGateway
            ->expects($this->once())
            ->method('getSensorIds')
            ->willReturn($sensorIds);

        $this->mockSensorGateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensorsRaw);

        $sensor->expects($this->once())
            ->method('formatValue')
            ->with($lastValue)
            ->willReturn($formattedValue);

        $sensor->expects($this->once())
            ->method('getEspeakText')
            ->with($lastValue)
            ->willReturn($formattedValue);

        $sensorValues = ['values'];
        $sensorsRaw[0]['espeak'] = true;
        $sensorsRaw[0]['last_value'] = $formattedValue;

        $this->mockSensorValuesGateway
            ->expects($this->once())
            ->method('getSensorValues')
            ->with($sensorId, $from)
            ->willReturn($sensorValues);

        $json = ['json'];
        $this->mockChart
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
            'available_sensors' => $sensors_obj,
            'from_intervals' => [
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
        $activeSensorIds   = null;
        $lastValue         = null;
        $type              = 'sensor_type';

        $request = new Request();
        $request->query->set('from', $from);

        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $sensors_raw = [
            [
                'id' => $sensorId = 12,
                'last_value' => $lastValue,
                'type' => $type,
            ]
        ];

        $sensors_obj = [
            $type => $sensor = $this->getMock(SensorInterface::class)
        ];

        $this->mockSensorBuilder
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensors_obj);

        $sensorIds = [$sensorId];
        $this->mockSensorGateway
            ->expects($this->once())
            ->method('getSensorIds')
            ->willReturn($sensorIds);

        $this->mockSensorGateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensors_raw);

        $sensors_raw[0]['espeak'] = false;

        $json = ['json'];
        $this->mockChart
            ->expects($this->once())
            ->method('formatJsonData')
            ->with($sensors_raw, [])
            ->willReturn($json);

        $actualResult = $this->subject->indexSensor($request, "13");

        $expectedResult = [
            'sensors' => $sensors_raw,
            'active_sensor_ids' => [13],
            'json' => $json,
            'current_from' => Chart::DEFAULT_TIME,
            'available_sensors' => $sensors_obj,
            'from_intervals' => [
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

        $this->mockVoBuilder
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
        $this->mockSensorGateway
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
            'type' => $sensor_type = 'type',
            'last_value' => $last_value = 'last value',
        ];

        $this->mockSensorGateway
            ->expects($this->once())
            ->method('getSensor')
            ->with($sensorId)
            ->willReturn($sensor);

        $mock_sensor = $this->getMockForAbstractClass(
            SensorInterface::class,
            ['getEspeakText']
        );

        $this->mockSensorBuilder
            ->expects($this->once())
            ->method('build')
            ->with($sensor_type)
            ->willReturn($mock_sensor);

        $mock_sensor
            ->expects($this->once())
            ->method('getEspeakText')
            ->willReturn($espeakText);

        $espeak_vo = new EspeakVO($espeakText);
        $event = new EspeakEvent($espeak_vo);

        $this->mockEventDispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $actualResult = $this->subject->espeak($request, $sensorId);

        $this->assertTrue($actualResult);
    }

    public function testSlim()
    {
        $sensorId              = 12;
        $sensorValueFormatted = 100;
        $sensorValue           = '100 grad';
        $type                   = 'sensor type';

        $request = new Request();

        $sensor_obj = $this->getMock(SensorInterface::class);
        $sensor_raw = [
            'type'       => $type,
            'last_value' => $sensorValue
        ];

        $this->mockSensorGateway
            ->expects($this->once())
            ->method('getSensor')
            ->with($sensorId)
            ->willReturn($sensor_raw);

        $this->mockSensorBuilder
            ->expects($this->once())
            ->method('build')
            ->with($type)
            ->willReturn($sensor_obj);

        $sensor_obj
            ->expects($this->once())
            ->method('getEspeakText')
            ->with($sensorValue)
            ->willReturn($sensorValueFormatted);

        $actualResult = $this->subject->slim($request, $sensorId);

        $expectedValue = [
            'sensor' => $sensor_raw,
            'sensor_value_formatted' => $sensorValueFormatted,
            'sensor_obj' => $sensor_obj,
            'refresh_interval' => 60
        ];

        $this->assertEquals($expectedValue, $actualResult);
    }

    public function testGetValue()
    {
        $sensorId              = 12;
        $sensorValueFormatted  = 100;
        $sensorValue           = '100 grad';
        $type                  = 'sensor type';

        $request = new Request();
        $request->query->set('sensor_id', $sensorId);

        $sensor_obj = $this->getMock(SensorInterface::class);
        $sensor_raw = [
            'type'       => $type,
            'last_value' => $sensorValue
        ];

        $this->mockSensorGateway
            ->expects($this->once())
            ->method('getSensor')
            ->with($sensorId)
            ->willReturn($sensor_raw);

        $this->mockSensorBuilder
            ->expects($this->once())
            ->method('build')
            ->with($type)
            ->willReturn($sensor_obj);

        $sensor_obj
            ->expects($this->once())
            ->method('getEspeakText')
            ->with($sensorValue)
            ->willReturn($sensorValueFormatted);

        $actualResult = $this->subject->getValue($request);

        $expectedValue = [
            'sensor' => $sensor_raw,
            'sensor_value_formatted' => $sensorValueFormatted,
            'sensor_obj' => $sensor_obj,
            'refresh_interval' => 60
        ];

        $this->assertEquals($expectedValue, $actualResult);
    }
}
