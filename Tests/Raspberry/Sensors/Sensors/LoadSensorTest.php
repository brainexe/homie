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
    private $_subject;

    public function setUp()
    {
        $this->_subject = new LoadSensor();
    }

    public function testGetSensorType()
    {
        $actual_result = $this->_subject->getSensorType();

        $this->assertEquals(LoadSensor::TYPE, $actual_result);
    }

    public function testGetValue()
    {
        $pin = 1;

        $actual_result = $this->_subject->getValue($pin);

        $this->assertTrue(is_numeric($actual_result));
    }

    public function testFormatValue()
    {
        $value = 1211.1112;

        $actual_result = $this->_subject->formatValue($value);

        $this->assertEquals('1211.1', $actual_result);
    }

    public function testGetEspeakText()
    {
        $value = 1211.1112;

        $actual_result = $this->_subject->getEspeakText($value);

        $this->assertEquals('1211.1', $actual_result);
    }

    public function testIsSupported()
    {
        $output = new DummyOutput();

        $actual_result = $this->_subject->isSupported($output);

        $this->assertTrue($actual_result);
    }
}
