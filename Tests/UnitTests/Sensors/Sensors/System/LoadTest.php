<?php

namespace Tests\Homie\Sensors\Sensors\System;

use Homie\Sensors\SensorVO;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Sensors\Sensors\System\Load;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

class LoadTest extends TestCase
{

    /**
     * @var Load
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Load();
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(Load::TYPE, $actualResult);
    }

    public function testGetValue()
    {
        $sensor = new SensorVO();
        $actualResult = $this->subject->getValue($sensor);

        $this->assertTrue(is_numeric($actualResult));
    }

    public function testIsSupported()
    {
        $output = new DummyOutput();
        $sensor = new SensorVO();
        $actual = $this->subject->isSupported($sensor, $output);

        $this->assertTrue($actual);
    }

    public function testSerialize()
    {
        $actual = json_encode($this->subject->jsonSerialize());

        $this->assertInternalType('string', $actual);
    }
}
