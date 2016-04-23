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
        $this->client = $this->getMock(ClientInterface::class);

        $this->subject = new Webcam($this->client);
    }

    public function testGetValue()
    {
        $this->client
            ->expects($this->once())
            ->method('execute');

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn('Maximum amplitude: 12');

        $sensor = new SensorVO();
        $sensor->parameter = 10;
        $actual = $this->subject->getValue($sensor);

        $this->assertEquals(21.58, $actual);
    }

    public function testIsSupported()
    {
        $parameter = null;

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;
        $actual = $this->subject->isSupported($sensor);

        $this->assertTrue($actual);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
