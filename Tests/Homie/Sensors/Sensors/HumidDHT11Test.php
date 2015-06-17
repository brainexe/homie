<?php

namespace Tests\Homie\Sensors\Sensors;

use Homie\Client\ClientInterface;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\Sensors\AbstractDHT11;
use Homie\Sensors\Sensors\HumidDHT11;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @covers Homie\Sensors\Sensors\HumidDHT11
 */
class HumidDHT11Test extends PHPUnit_Framework_TestCase
{

    /**
     * @var HumidDHT11
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

        $this->subject = new HumidDHT11($this->client, $this->fileSystem, '/ada/');
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(HumidDHT11::TYPE, $actualResult);
    }

    public function testGetValueWitValidOutput()
    {
        $humid     = 70;
        $parameter = 3;
        $output    = "Hum = $humid %";

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn($output);

        $actualResult = $this->subject->getValue($parameter);

        $this->assertEquals($humid, $actualResult);
    }

    public function testIsSupported()
    {
        $file = 'mockFile';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(true);

        $output = new DummyOutput();
        $actualResult = $this->subject->isSupported($file, $output);

        $this->assertTrue($actualResult);
    }

    public function testIsSupportedWhenNotSupported()
    {
        $file = 'mockFile';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(false);

        $output = new DummyOutput();
        $actualResult = $this->subject->isSupported($file, $output);

        $this->assertFalse($actualResult);
    }

}
