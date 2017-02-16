<?php

namespace Tests\Homie\Sensors\Sensors\System;

use Homie\Client\ClientInterface;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\System\DiskUsedPercent;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Homie\Sensors\Sensors\System\DiskUsedPercent
 */
class DiskUsedPercentTest extends TestCase
{

    /**
     * @var DiskUsedPercent
     */
    private $subject;

    /**
     * @var MockObject|ClientInterface
     */
    private $client;

    public function setUp()
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->subject = new DiskUsedPercent($this->client);
    }

    public function testGetSensorType()
    {
        $actual = $this->subject->getSensorType();
        $this->assertEquals(DiskUsedPercent::TYPE, $actual);
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
            ->willReturn('foo bar 12%');

        $sensor = new SensorVO();
        $actual = $this->subject->getValue($sensor);

        $this->assertEquals(12, $actual);
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
