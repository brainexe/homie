<?php

namespace Tests\Homie\Sensors\Sensors\Barometer;

use Homie\Client\ClientInterface;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\Barometer\BMP085;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Homie\Sensors\Sensors\Barometer\BMP085
 */
class BMP085Test extends TestCase
{

    /**
     * @var BMP085
     */
    private $subject;

    /**
     * @var ClientInterface|MockObject
     */
    private $client;

    public function setUp()
    {
        $this->client = $this->createMock(ClientInterface::class);

        $this->subject = new BMP085($this->client);
    }

    public function testGetValue()
    {
        $parameter = "foo.sh";

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with($parameter)
            ->willReturn('Pressure: 1024 hPa');

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;
        $actual = $this->subject->getValue($sensor);

        $this->assertEquals(1024, $actual);
    }

    /**
     * @expectedException \Homie\Sensors\Exception\InvalidSensorValueException
     * @expectedExceptionMessage Invalid response:
     */
    public function testGetValueWithoutValue()
    {
        $parameter = "foo.sh";

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with($parameter)
            ->willReturn('');

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;
        $this->subject->getValue($sensor);
    }

    /**
     * @expectedException \Homie\Sensors\Exception\InvalidSensorValueException
     * @expectedExceptionMessage Invalid response: invalid
     */
    public function testGetValueInvalidValue()
    {
        $parameter = "foo.sh";

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with($parameter)
            ->willReturn('invalid');

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;

        $this->subject->getValue($sensor);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
