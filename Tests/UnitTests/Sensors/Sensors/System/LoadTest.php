<?php

namespace Tests\Homie\Sensors\Sensors\System;

use Homie\Sensors\SensorVO;
use PHPUnit\Framework\TestCase;
use Homie\Sensors\Sensors\System\Load;

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
        $sensor = new SensorVO();
        $actual = $this->subject->isSupported($sensor);

        $this->assertTrue($actual);
    }

    public function testSerialize()
    {
        $actual = json_encode($this->subject->jsonSerialize());

        $this->assertInternalType('string', $actual);
    }
}
