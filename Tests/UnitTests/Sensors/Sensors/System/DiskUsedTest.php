<?php

namespace Tests\Homie\Sensors\Sensors\System;

use Homie\Client\ClientInterface;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\System\DiskUsed;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Sensors\Sensors\System\DiskUsed
 */
class DiskUsedTest extends TestCase
{

    /**
     * @var DiskUsed
     */
    private $subject;

    /**
     * @var MockObject|ClientInterface
     */
    private $client;

    public function setUp()
    {
        $this->client = $this->getMock(ClientInterface::class);
        $this->subject = new DiskUsed($this->client);
    }

    public function testGetSensorType()
    {
        $actual = $this->subject->getSensorType();
        $this->assertEquals(DiskUsed::TYPE, $actual);
    }

    /**
     * @expectedException \Homie\Sensors\Exception\InvalidSensorValueException
     * @expectedExceptionMessage No disk value found: ads
     */
    public function testGetValueInvalid()
    {
        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn('ads');

        $sensor = new SensorVO();
        $this->subject->getValue($sensor);
    }

    public function testGetValue()
    {
        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn('120kb');

        $sensor = new SensorVO();
        $actual = $this->subject->getValue($sensor);

        $this->assertEquals(120000, $actual);
    }

    public function testIsSupported()
    {
        $sensor = new SensorVO();
        $actual = $this->subject->isSupported($sensor);
        $this->assertTrue($actual);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
