<?php

namespace Tests\Homie\Sensors\Sensors\Temperature;

use Homie\Client\ClientInterface;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\Temperature\DHT11;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;
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
        $this->client     = $this->getMock(ClientInterface::class, [], [], '', false);
        $this->fileSystem = $this->getMock(Filesystem::class, [], [], '', false);

        $this->subject = new DHT11($this->client, $this->fileSystem);
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(DHT11::TYPE, $actualResult);
    }

    public function testGetValueWitValidOutput()
    {
        $temp      = "70.30001";
        $parameter = 3;
        $output    = "Temperature = $temp °";

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn($output);

        $actualResult = $this->subject->getValue($parameter);

        $this->assertEquals(70.3, $actualResult);
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

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
