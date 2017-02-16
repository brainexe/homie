<?php

namespace Tests\Homie\Sensors\Formatter;

use PHPUnit\Framework\TestCase;
use Homie\Sensors\Formatter\Barometer;

/**
 * @covers \Homie\Sensors\Formatter\Barometer
 */
class BarometerTest extends TestCase
{

    /**
     * @var Barometer
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Barometer();
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
        $this->assertEquals(Barometer::TYPE, $actualResult);
    }

    /**
     * @return array[]
     */
    public function provideEspeak()
    {
        return [
            ['12', "12hPa"],
            ['12.22', "12.22hPa"],
            [12.22, "12.22hPa"],
            [12.9999, "12.9999hPa"],
        ];
    }

    /**
     * @return array[]
     */
    public function provideValues()
    {
        return [
            ['12', "12hPa"],
            ['12.22', "12.22hPa"],
            [12.22, "12.22hPa"],
            [12.9999, "12.9999hPa"],
        ];
    }
}
