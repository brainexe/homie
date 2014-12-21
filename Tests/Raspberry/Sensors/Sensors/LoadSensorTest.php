<?php

namespace Tests\Raspberry\Sensors\Sensors\LoadSensor;

use PHPUnit_Framework_TestCase;

use Raspberry\Sensors\Sensors\LoadSensor;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @Covers Raspberry\Sensors\Sensors\LoadSensor
 */
class LoadSensorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var LoadSensor
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new LoadSensor();
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(LoadSensor::TYPE, $actualResult);
    }

    public function testGetValue()
    {
        $pin = 1;

        $actualResult = $this->subject->getValue($pin);

        $this->assertTrue(is_numeric($actualResult));
    }

    public function testFormatValue()
    {
        $value = 1211.1112;

        $actualResult = $this->subject->formatValue($value);

        $this->assertEquals('1211.1', $actualResult);
    }

    public function testGetEspeakText()
    {
        $value = 1211.1112;

        $actualResult = $this->subject->getEspeakText($value);

        $this->assertEquals('1211.1', $actualResult);
    }

    public function testIsSupported()
    {
        $output = new DummyOutput();

        $actualResult = $this->subject->isSupported($output);

        $this->assertTrue($actualResult);
    }
}
