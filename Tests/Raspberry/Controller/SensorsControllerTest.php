<?php

namespace Tests\Raspberry\Controller\SensorsController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Controller\SensorsController;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Raspberry\Sensors\Sensors\SensorInterface;
use Raspberry\Sensors\SensorVO;

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

    public function setUp()
    {
        $this->mockSensorGateway = $this->getMock(SensorGateway::class, [], [], '', false);
        $this->mockSensorValuesGateway = $this->getMock(SensorValuesGateway::class, [], [], '', false);
        $this->mockChart = $this->getMock(Chart::class, [], [], '', false);
        $this->mockSensorBuilder = $this->getMock(SensorBuilder::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new SensorsController(
            $this->mockSensorGateway,
            $this->mockSensorValuesGateway,
            $this->mockChart,
            $this->mockSensorBuilder
        );
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testIndexSensor()
    {
        $from              = 10;
        $active_sensor_ids = null;
        $last_value        = 100;
        $formatted_value   = '100 grad';
        $type              = 'sensor_type';

        $request = new Request();
        $request->query->set('from', $from);

        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $sensors_raw = [
        [
        'id' => $sensorId = 12,
        'last_value' => $last_value,
        'type' => $type,
        ]
        ];

        $sensors_obj = [
        $type => $sensor = $this->getMock(SensorInterface::class)
        ];

        $this->mockSensorBuilder
        ->expects($this->once())
        ->method('getSensors')
        ->will($this->returnValue($sensors_obj));

        $sensorIds = [$sensorId];
        $this->mockSensorGateway
        ->expects($this->once())
        ->method('getSensorIds')
        ->will($this->returnValue($sensorIds));

        $this->mockSensorGateway
        ->expects($this->once())
        ->method('getSensors')
        ->will($this->returnValue($sensors_raw));

        $sensor->expects($this->once())
        ->method('formatValue')
        ->with($last_value)
        ->will($this->returnValue($formatted_value));

        $sensor->expects($this->once())
        ->method('getEspeakText')
        ->with($last_value)
        ->will($this->returnValue($formatted_value));

        $sensor_values = ['values'];
        $sensors_raw[0]['espeak'] = true;
        $sensors_raw[0]['last_value'] = $formatted_value;

        $this->mockSensorValuesGateway
        ->expects($this->once())
        ->method('getSensorValues')
        ->with($sensorId, $from)
        ->will($this->returnValue($sensor_values));

        $json = ['json'];
        $this->mockChart
        ->expects($this->once())
        ->method('formatJsonData')
        ->with($sensors_raw, [$sensorId => $sensor_values])
        ->will($this->returnValue($json));

        $actualResult = $this->subject->indexSensor($request, $active_sensor_ids);

        $expectedResult = [
        'sensors' => $sensors_raw,
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
        ]];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testIndexSensorWithoutFromAndLastValue()
    {
        $from              = null;
        $active_sensor_ids = null;
        $last_value        = null;
        $type              = 'sensor_type';

        $request = new Request();
        $request->query->set('from', $from);

        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $sensors_raw = [
        [
        'id' => $sensorId = 12,
        'last_value' => $last_value,
        'type' => $type,
        ]
        ];

        $sensors_obj = [
        $type => $sensor = $this->getMock(SensorInterface::class)
        ];

        $this->mockSensorBuilder
        ->expects($this->once())
        ->method('getSensors')
        ->will($this->returnValue($sensors_obj));

        $sensorIds = [$sensorId];
        $this->mockSensorGateway
        ->expects($this->once())
        ->method('getSensorIds')
        ->will($this->returnValue($sensorIds));

        $this->mockSensorGateway
        ->expects($this->once())
        ->method('getSensors')
        ->will($this->returnValue($sensors_raw));

        $sensors_raw[0]['espeak'] = false;

        $json = ['json'];
        $this->mockChart
        ->expects($this->once())
        ->method('formatJsonData')
        ->with($sensors_raw, [])
        ->will($this->returnValue($json));

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
        ]];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAddSensor()
    {
        $type = 'type';
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
        $sensorVo->name        = $name;
        $sensorVo->type        = $type;
        $sensorVo->description = $description;
        $sensorVo->pin         = $pin;
        $sensorVo->interval    = $interval;
        $sensorVo->node        = $node;

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
        $espeak_text = 'last value';

        $sensor = [
        'type' => $sensor_type = 'type',
        'last_value' => $last_value = 'last value',
        ];

        $this->mockSensorGateway
        ->expects($this->once())
        ->method('getSensor')
        ->with($sensorId)
        ->will($this->returnValue($sensor));

        $mock_sensor = $this->getMockForAbstractClass(SensorInterface::class, ['getEspeakText']);

        $this->mockSensorBuilder
        ->expects($this->once())
        ->method('build')
        ->with($sensor_type)
        ->will($this->returnValue($mock_sensor));

        $mock_sensor
        ->expects($this->once())
        ->method('getEspeakText')
        ->will($this->returnValue($espeak_text));

        $espeak_vo = new EspeakVO($espeak_text);
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
        $sensor_value_formatted = 100;
        $sensor_value           = '100 grad';
        $type                   = 'sensor type';

        $request = new Request();

        $sensor_obj = $this->getMock(SensorInterface::class);
        $sensor_raw = [
        'type'       => $type,
        'last_value' => $sensor_value
        ];

        $this->mockSensorGateway
        ->expects($this->once())
        ->method('getSensor')
        ->with($sensorId)
        ->will($this->returnValue($sensor_raw));

        $this->mockSensorBuilder
        ->expects($this->once())
        ->method('build')
        ->with($type)
        ->will($this->returnValue($sensor_obj));

        $sensor_obj
        ->expects($this->once())
        ->method('getEspeakText')
        ->with($sensor_value)
        ->will($this->returnValue($sensor_value_formatted));

        $actualResult = $this->subject->slim($request, $sensorId);

        $expected_value = [
        'sensor' => $sensor_raw,
        'sensor_value_formatted' => $sensor_value_formatted,
        'sensor_obj' => $sensor_obj,
        'refresh_interval' => 60
        ];

        $this->assertEquals($expected_value, $actualResult);
    }

    public function testGetValue()
    {
        $sensorId              = 12;
        $sensor_value_formatted = 100;
        $sensor_value           = '100 grad';
        $type                   = 'sensor type';

        $request = new Request();
        $request->query->set('sensor_id', $sensorId);

        $sensor_obj = $this->getMock(SensorInterface::class);
        $sensor_raw = [
        'type'       => $type,
        'last_value' => $sensor_value
        ];

        $this->mockSensorGateway
        ->expects($this->once())
        ->method('getSensor')
        ->with($sensorId)
        ->will($this->returnValue($sensor_raw));

        $this->mockSensorBuilder
        ->expects($this->once())
        ->method('build')
        ->with($type)
        ->will($this->returnValue($sensor_obj));

        $sensor_obj
        ->expects($this->once())
        ->method('getEspeakText')
        ->with($sensor_value)
        ->will($this->returnValue($sensor_value_formatted));

        $actualResult = $this->subject->getValue($request);

        $expected_value = [
        'sensor' => $sensor_raw,
        'sensor_value_formatted' => $sensor_value_formatted,
        'sensor_obj' => $sensor_obj,
        'refresh_interval' => 60
        ];

        $this->assertEquals($expected_value, $actualResult);
    }
}
