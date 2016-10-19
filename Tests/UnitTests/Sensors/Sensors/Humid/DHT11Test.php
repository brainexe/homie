<?php

namespace Tests\Homie\Sensors\Sensors\Humid;

use Homie\Client\ClientInterface;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\Humid\DHT11;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_TestCase as TestCase;
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

        $this->subject = new DHT11($this->client, $this->fileSystem, '/ada/');
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(DHT11::TYPE, $actualResult);
    }

    public function testGetValueWitValidOutput()
    {
        $humid     = 70;
        $parameter = 3;
        $output    = "Hum = $humid %";

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn($output);

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;
        $actual = $this->subject->getValue($sensor);

        $this->assertEquals($humid, $actual);
    }

    /**
     * @expectedException \Homie\Sensors\Exception\InvalidSensorValueException
     * @expectedExceptionMessage Invalid humidity value: Hum = %
     */
    public function testGetValueWitInvalidOutput()
    {
        $parameter = 3;
        $output    = "Hum = %";

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn($output);

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;
        $this->subject->getValue($sensor);
    }

    /**
     * @expectedException \Homie\Sensors\Exception\InvalidSensorValueException
     * @expectedExceptionMessage Invalid humidity value: Hum = 120 %
     */
    public function testGetValueWitTooHighValue()
    {
        $parameter = 3;
        $output    = "Hum = 120 %";

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn($output);

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;
        $this->subject->getValue($sensor);
    }

    public function testIsSupported()
    {
        $file = 'mockFile';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(true);

        $sensor = new SensorVO();
        $sensor->parameter = $file;

        $actual = $this->subject->isSupported($sensor);

        $this->assertTrue($actual);
    }

    /**
     * @expectedException  \Homie\Sensors\Exception\InvalidSensorValueException
     * @expectedExceptionMessage humid.dht11: Script not exists: mockFile
     */
    public function testIsSupportedWhenNotSupported()
    {
        $file = 'mockFile';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(false);

        $sensor = new SensorVO();
        $sensor->parameter = $file;

        $this->subject->isSupported($sensor);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
