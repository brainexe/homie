<?php

namespace Tests\Homie\Sensors\Formatter;

use Homie\Sensors\Formatter\Number;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Sensors\Formatter\Number
 */
class NumberTest extends TestCase
{

    /**
     * @var Number
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Number();
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
        $this->assertEquals(Number::TYPE, $actualResult);
    }

    /**
     * @return array[]
     */
    public function provideEspeak()
    {
        return [
            [1000000, "1M"],
            [1234000, "1M"],
        ];
    }

    /**
     * @return array[]
     */
    public function provideValues()
    {

        return [
            [1000000, "1M"],
            [1234000, "1M"],
        ];
    }
}
