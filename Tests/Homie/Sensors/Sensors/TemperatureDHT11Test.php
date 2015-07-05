<?php

namespace Tests\Homie\Sensors\Sensors;

use Homie\Client\ClientInterface;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\Sensors\TemperatureDHT11;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class TemperatureDHT11Test extends TestCase
{

    /**
     * @var TemperatureDHT11
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
        $this->client     = $this->getMock(ClientInterface::class, [], [], '', false);
        $this->fileSystem = $this->getMock(Filesystem::class, [], [], '', false);

        $this->subject = new TemperatureDHT11($this->client, $this->fileSystem);
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(TemperatureDHT11::TYPE, $actualResult);
    }

    public function testGetValueWitValidOutput()
    {
        $temp      = 70;
        $parameter = 3;
        $output    = "Temp = $temp %";

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn($output);

        $actualResult = $this->subject->getValue($parameter);

        $this->assertEquals($temp, $actualResult);
    }

    public function testGetValueWitInvalidOutput()
    {
        $parameter = 3;
        $output    = "Temp = ERROR %";

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn($output);

        $actual = $this->subject->getValue($parameter);

        $this->assertEquals(null, $actual);
    }

    public function testIsSupportedExisting()
    {
        $output    = new DummyOutput();
        $parameter = 'parameter';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($parameter)
            ->willReturn(true);

        $actual = $this->subject->isSupported($parameter, $output);

        $this->assertTrue($actual);
    }

    public function testIsSupportedNotExisting()
    {
        $output    = new DummyOutput();
        $parameter = 'parameter';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($parameter)
            ->willReturn(false);

        $actual = $this->subject->isSupported($parameter, $output);

        $this->assertFalse($actual);
    }
}
