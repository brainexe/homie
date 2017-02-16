<?php

namespace Tests\Homie\Sensors\Sensors\Temperature;

use Homie\Client\ClientInterface;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\Temperature\DHT11;
use Homie\Sensors\SensorVO;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\Filesystem\Filesystem;

class DHT11Test extends TestCase
{

    /**
     * @var DHT11
     */
    private $subject;

    /**
     * @var ClientInterface|MockObject
     */
    private $client;

    /**
     * @var Filesystem|MockObject
     */
    private $fileSystem;

    public function setUp()
    {
        $this->client     = $this->createMock(ClientInterface::class);
        $this->fileSystem = $this->createMock(Filesystem::class);

        $this->subject = new DHT11($this->client, $this->fileSystem);
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(DHT11::TYPE, $actualResult);
    }

    public function testGetValueWitValidOutput()
    {
        $temp      = "70.30001";
        $parameter = 3;
        $output    = "Temperature = $temp °";

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn($output);

        $actualResult = $this->subject->getValue($sensor);

        $this->assertEquals(70.3, $actualResult);
    }

    public function testGetValueWitValidNegativeOutput()
    {
        $parameter = 3;
        $output    = "Temperature = -1.80 *C";

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn($output);

        $actualResult = $this->subject->getValue($sensor);

        $this->assertEquals(-1.8, $actualResult);
    }

    /**
     * @expectedException \Homie\Sensors\Exception\InvalidSensorValueException
     * @expectedExceptionMessage Invalid value: Temp = ERROR %
     */
    public function testGetValueWitInvalidOutput()
    {
        $parameter = 3;
        $output    = "Temp = ERROR %";

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn($output);

        $this->subject->getValue($sensor);
    }

    /**
     * @expectedException \Homie\Sensors\Exception\InvalidSensorValueException
     * @expectedExceptionMessage Invalid value: Temp = -180 *C
     */
    public function testGetValueWitInvalidRange()
    {
        $parameter = 3;
        $output    = "Temp = -180 *C";

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn($output);

        $this->subject->getValue($sensor);
    }

    public function testIsSupportedExisting()
    {
        $parameter = 'parameter';

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($parameter)
            ->willReturn(true);

        $actual = $this->subject->isSupported($sensor);

        $this->assertTrue($actual);
    }

    /**
     * @expectedException \Homie\Sensors\Exception\InvalidSensorValueException
     * @expectedExceptionMessage temperature.dht11: Script not exists: parameter
     */
    public function testIsSupportedNotExisting()
    {
        $parameter = 'parameter';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($parameter)
            ->willReturn(false);

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;

        $actual = $this->subject->isSupported($sensor);

        $this->assertFalse($actual);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
