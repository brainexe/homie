<?php

namespace Raspberry\Tests\Sensors;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Sensors\Interfaces\Sensor;
use Raspberry\Sensors\SensorBuilder;

class SensorBuilderTest extends TestCase
{

    /**
     * @var SensorBuilder
     */
    private $subject;


    public function setUp()
    {
        $this->subject = new SensorBuilder();
    }

    public function testGetSensors()
    {
        /** @var Sensor|MockObject $sensorMock */
        $sensorMock = $this->getMock(Sensor::class);
        $sensorType = 'sensor_123';

        $this->subject->addSensor($sensorType, $sensorMock);
        $actualResult = $this->subject->getSensors();

        $this->assertEquals([$sensorType => $sensorMock], $actualResult);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid sensor type: sensor_123
     */
    public function testBuildInvalid()
    {
        $sensorType = 'sensor_123';

        $this->subject->build($sensorType);
    }

    public function testBuildValid()
    {
        /** @var Sensor|MockObject $sensorMock */
        $sensorMock = $this->getMock(Sensor::class);
        $sensorType = 'sensor_123';

        $this->subject->addSensor($sensorType, $sensorMock);

        $actualResult = $this->subject->build($sensorType);

        $this->assertEquals($sensorMock, $actualResult);
    }
}
