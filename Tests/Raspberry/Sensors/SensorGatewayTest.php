<?php

namespace Tests\Raspberry\Sensors\SensorGateway;

use BrainExe\Core\Redis\Redis;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorVO;

/**
 * @Covers Raspberry\Sensors\SensorGateway
 */
class SensorGatewayTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SensorGateway
     */
    private $subject;

    /**
     * @var Redis|MockObject
     */
    private $mockRedis;


    public function setUp()
    {
        $this->mockRedis = $this->getMock(Redis::class, [], [], '', false);

        $this->subject = new SensorGateway();
        $this->subject->setRedis($this->mockRedis);
    }

    public function testGetSensors()
    {
        $sensorIds = [
        $sensorId = 10
        ];

        $result = ['result'];

        $this->mockRedis
            ->expects($this->once())
            ->method('SMEMBERS')
            ->with(SensorGateway::SENSOR_IDS)
            ->willReturn($sensorIds);

        $this->mockRedis
            ->expects($this->once())
            ->method('multi')
            ->willReturn($this->mockRedis);

        $this->mockRedis
            ->expects($this->once())
            ->method('HGETALL')
            ->with("sensor:$sensorId");

        $this->mockRedis
            ->expects($this->once())
            ->method('exec')
            ->willReturn($result);

        $actualResult = $this->subject->getSensors();

        $this->assertEquals($result, $actualResult);
    }

    public function testGetSensorsForNode()
    {
        $node = 1;
        $sensorIds = [
        $sensorId = 10
        ];

        $result = [
        [
        'node' => 100
        ],
        [
        'node' => $node
        ]
        ];

        $this->mockRedis
            ->expects($this->once())
            ->method('SMEMBERS')
            ->with(SensorGateway::SENSOR_IDS)
            ->willReturn($sensorIds);

        $this->mockRedis
            ->expects($this->once())
            ->method('multi')
            ->willReturn($this->mockRedis);

        $this->mockRedis
            ->expects($this->once())
            ->method('HGETALL')
            ->with("sensor:$sensorId");

        $this->mockRedis
            ->expects($this->once())
            ->method('exec')
            ->willReturn($result);

        $actualResult = $this->subject->getSensorsForNode($node);

        $expectedResult = [
        1 => [
        'node' => $node
        ]
        ];
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetSensorIds()
    {
        $sensorIds = [
        $sensorId = 10
        ];

        $this->mockRedis
            ->expects($this->once())
            ->method('SMEMBERS')
            ->with(SensorGateway::SENSOR_IDS)
            ->willReturn($sensorIds);

        $actualResult = $this->subject->getSensorIds();

        $this->assertEquals($sensorIds, $actualResult);
    }

    public function testAddSensor()
    {
        $sensorVo = new SensorVO();
        $sensorIds = [
        $last_sensor_id = 10
        ];

        $new_sensor_id = 11;

        $sensor_data = (array)$sensorVo;
        $sensor_data['id'] = $new_sensor_id;
        $sensor_data['last_value'] = 0;
        $sensor_data['last_value_timestamp'] = 0;

        $this->mockRedis
            ->expects($this->once())
            ->method('SMEMBERS')
            ->with(SensorGateway::SENSOR_IDS)
            ->willReturn($sensorIds);

        $this->mockRedis
            ->expects($this->once())
            ->method('multi')
            ->willReturn($this->mockRedis);

        $this->mockRedis
            ->expects($this->once())
            ->method('HMSET')
            ->with("sensor:$new_sensor_id");

        $this->mockRedis
            ->expects($this->once())
            ->method('sAdd')
            ->with(SensorGateway::SENSOR_IDS, $new_sensor_id);

        $this->mockRedis
            ->expects($this->once())
            ->method('exec');

        $actualResult = $this->subject->addSensor($sensorVo);

        $this->assertEquals($new_sensor_id, $actualResult);
    }

    public function testGetSensor()
    {
        $sensorId = 10;
        $sensor = ['sensor'];

        $this->mockRedis
            ->expects($this->once())
            ->method('hGetAll')
            ->with("sensor:$sensorId")
            ->willReturn($sensor);

        $actualResult = $this->subject->getSensor($sensorId);

        $this->assertEquals($sensor, $actualResult);
    }

    public function testDeleteSensor()
    {
        $sensorId = 10;

        $this->mockRedis
            ->expects($this->at(0))
            ->method('del')
            ->with("sensor:$sensorId");

        $this->mockRedis
            ->expects($this->at(1))
            ->method('sRem')
            ->with(SensorGateway::SENSOR_IDS, $sensorId);

        $this->mockRedis
            ->expects($this->at(2))
            ->method('del')
            ->with("sensor_values:$sensorId");

        $this->subject->deleteSensor($sensorId);
    }
}
