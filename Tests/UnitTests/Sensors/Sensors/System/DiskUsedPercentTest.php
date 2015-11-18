<?php

namespace Tests\Homie\Sensors\Sensors\System;

use Homie\Client\ClientInterface;
use Homie\Sensors\Sensors\System\DiskUsedPercent;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @covers Homie\Sensors\Sensors\System\DiskUsedPercent
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
        $this->client = $this->getMock(ClientInterface::class);
        $this->subject = new DiskUsedPercent($this->client);
    }

    public function testGetSensorType()
    {
        $actual = $this->subject->getSensorType();
        $this->assertEquals(DiskUsedPercent::TYPE, $actual);
    }

    public function testGetValueInvalid()
    {
        $pin = 1;

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn('ads');

        $actual = $this->subject->getValue($pin);

        $this->assertNull($actual);
    }

    public function testGetValue()
    {
        $pin = 1;

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn('foo bar 12%');

        $actual = $this->subject->getValue($pin);

        $this->assertEquals(12, $actual);
    }

    public function testIsSupported()
    {
        $output = new DummyOutput();
        $actual = $this->subject->isSupported('', $output);
        $this->assertTrue($actual);
    }
}
