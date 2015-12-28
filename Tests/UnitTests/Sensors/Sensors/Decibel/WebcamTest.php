<?php

namespace Tests\Homie\Sensors\Sensors\Decibel;

use Homie\Client\ClientInterface;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\Decibel\Webcam;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Output\NullOutput;

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
            ->method('executeWithReturn')
            ->willReturn(10);

        $sensor = new SensorVO();
        $sensor->parameter = 10;
        $actual = $this->subject->getValue($sensor);

        $this->assertEquals(20, $actual);
    }

    public function testIsSupported()
    {
        $parameter = null;
        $output = new NullOutput();

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;
        $actual = $this->subject->isSupported($sensor, $output);

        $this->assertTrue($actual);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
