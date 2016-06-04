<?php

namespace Tests\Homie\Sensors\Sensors\Decibel;

use Homie\Client\ClientInterface;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\Decibel\Webcam;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Sensors\Sensors\Decibel\Webcam
 */
class WebcamTest extends TestCase
{

    /**
     * @var Webcam
     */
    private $subject;

    /**
     * @var ClientInterface|MockObject
     */
    private $client;

    public function setUp()
    {
        $this->client = $this->createMock(ClientInterface::class);

        $this->subject = new Webcam($this->client);
    }

    public function testGetValue()
    {
        $this->client
            ->expects($this->at(0))
            ->method('executeWithReturn');

        $this->client
            ->expects($this->at(1))
            ->method('executeWithReturn')
            ->willReturn('Maximum amplitude: 12');

        $sensor = new SensorVO();
        $sensor->parameter = 10;
        $actual = $this->subject->getValue($sensor);

        $this->assertEquals(21.58, $actual);
    }

    /**
     * @expectedException \Homie\Sensors\Exception\InvalidSensorValueException
     * @expectedExceptionMessage No Maximum amplitude found: invalid value
     */
    public function testGetInvalidValue()
    {
        $this->client
            ->expects($this->at(0))
            ->method('executeWithReturn');

        $this->client
            ->expects($this->at(1))
            ->method('executeWithReturn')
            ->willReturn('invalid value');

        $sensor = new SensorVO();
        $sensor->parameter = 10;

        $this->subject->getValue($sensor);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
