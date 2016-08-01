<?php

namespace Tests\Homie\Sensors\Formatter;

use PHPUnit_Framework_TestCase as TestCase;
use Homie\Sensors\Formatter\Temperature;

/**
 * @covers Homie\Sensors\Formatter\Temperature
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

    public function testGetType()
    {
        $actualResult = $this->subject->getType();
        $this->assertEquals(Temperature::TYPE, $actualResult);
    }

    /**
     * @return array[]
     */
    public function provideValues()
    {
        return [
            ['12',     "12°"],
            ['-12.2',  "-12.2°"],
            ['12.22',  "12.22°"],
            ['12.2',   "12.2°"],
            ['0',      "0°"],
        ];
    }
}
