<?php

namespace Tests\Homie\Sensors\Sensors\System;

use Homie\Client\ClientInterface;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\System\DiskUsed;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

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
            ->willReturn('120kb');

        $actual = $this->subject->getValue($pin);

        $this->assertEquals(120000, $actual);
    }

    public function testIsSupported()
    {
        $output = new DummyOutput();
        $actual = $this->subject->isSupported('', $output);
        $this->assertTrue($actual);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
