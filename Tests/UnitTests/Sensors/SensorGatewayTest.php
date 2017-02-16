<?php

namespace Tests\Homie\Sensors;

use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\IdGenerator;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorVO;

/**
 * @covers \Homie\Sensors\SensorGateway
 */
class SensorGatewayTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var SensorGateway
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    public function setUp()
    {
        $this->redis       = $this->getRedisMock();
        $this->idGenerator = $this->createMock(IdGenerator::class);

        $this->subject = new SensorGateway();
        $this->subject->setRedis($this->redis);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testGetSensors()
    {
        $sensorIds = [
            $sensorId = 10
        ];

        $result = ['result'];

        $this->redis
            ->expects($this->once())
            ->method('smembers')
            ->with(SensorGateway::SENSOR_IDS)
            ->willReturn($sensorIds);

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->once())
            ->method('hgetall')
            ->with("sensor:$sensorId");

        $this->redis
            ->expects($this->once())
            ->method('execute')
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

        $this->redis
            ->expects($this->once())
            ->method('smembers')
            ->with(SensorGateway::SENSOR_IDS)
            ->willReturn($sensorIds);

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->once())
            ->method('hgetall')
            ->with("sensor:$sensorId");

        $this->redis
            ->expects($this->once())
            ->method('execute')
            ->willReturn($result);

        $actual = $this->subject->getSensorsForNode($node);

        $expected = [
            1 => [
                'node' => $node
            ]
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testGetSensorIds()
    {
        $sensorIds = [
            10
        ];

        $this->redis
            ->expects($this->once())
            ->method('smembers')
            ->with(SensorGateway::SENSOR_IDS)
            ->willReturn($sensorIds);

        $actual = $this->subject->getSensorIds();

        $this->assertEquals($sensorIds, $actual);
    }

    public function testAddSensor()
    {
        $sensorVo    = new SensorVO();
        $newSensorId = 11880;

        $this->idGenerator
            ->expects($this->once())
            ->method('generateUniqueId')
            ->willReturn($newSensorId);

        $sensorVo->sensorId = $newSensorId;
        $sensorVo->lastValueTimestamp = 0;
        $sensorVo->lastValue = 0;

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->once())
            ->method('hmset')
            ->with("sensor:$newSensorId");

        $this->redis
            ->expects($this->once())
            ->method('sadd')
            ->with(SensorGateway::SENSOR_IDS, [$newSensorId]);

        $this->redis
            ->expects($this->once())
            ->method('execute');

        $actualResult = $this->subject->addSensor($sensorVo);

        $this->assertEquals($newSensorId, $actualResult);
    }

    public function testGetSensor()
    {
        $sensorId = 10;
        $sensor = ['sensor'];

        $this->redis
            ->expects($this->once())
            ->method('hgetall')
            ->with("sensor:$sensorId")
            ->willReturn($sensor);

        $actual = $this->subject->getSensor($sensorId);

        $this->assertEquals($sensor, $actual);
    }

    public function testSave()
    {
        $sensorVo = new SensorVO();
        $sensorVo->sensorId = 12;

        $this->redis
            ->expects($this->once())
            ->method('hmset')
            ->with("sensor:12");

        $this->subject->save($sensorVo);
    }

    public function testDeleteSensor()
    {
        $sensorId = 10;

        $this->redis
            ->expects($this->at(0))
            ->method('del')
            ->with("sensor:$sensorId");

        $this->redis
            ->expects($this->at(1))
            ->method('srem')
            ->with(SensorGateway::SENSOR_IDS, $sensorId);

        $this->redis
            ->expects($this->at(2))
            ->method('del')
            ->with("sensor_values:$sensorId");

        $this->subject->deleteSensor($sensorId);
    }
}
