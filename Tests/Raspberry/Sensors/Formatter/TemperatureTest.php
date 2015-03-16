<?php

namespace Tests\Raspberry\Sensors\Formatter;

use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Sensors\Formatter\Temperature;

/**
 * @Covers Raspberry\Sensors\Formatter\Temperature
 */
class TemperatureTest extends TestCase
{

    /**
     * @var Temperature
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Temperature();
    }

    /**
     * @dataProvider provideValues
     * @param mixed $value
     * @param string $expected
     */
    public function testFormatValue($value, $expected)
    {
        $actual = $this->subject->formatValue($value);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider provideEspeak
     * @param string $value
     * @param string $expected
     */
    public function testGetEspeakText($value, $expected)
    {
        $actual = $this->subject->getEspeakText($value);

        $this->assertEquals($expected, $actual);
    }

    public function testGetType()
    {
        $actualResult = $this->subject->getType();
        $this->assertEquals(Temperature::TYPE, $actualResult);
    }

    /**
     * @return array[]
     */
    public function provideEspeak()
    {
        return [
            ['12', "12,0 Degree"],
            ['12.22', "12,2 Degree"],
            [12.22, "12,2 Degree"],
            [12.9999, "13,0 Degree"],
        ];
    }

    /**
     * @return array[]
     */
    public function provideValues()
    {
        return [
            ['12', "12.0°"],
            ['12.22', "12.2°"],
            [12.22, "12.2°"],
            [12.9999, "13.0°"],
        ];
    }
}
