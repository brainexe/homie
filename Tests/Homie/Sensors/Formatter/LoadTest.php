<?php

namespace Tests\Homie\Sensors\Formatter;

use PHPUnit_Framework_TestCase as TestCase;
use Homie\Sensors\Formatter\Load;

/**
 * @covers Homie\Sensors\Formatter\Barometer
 */
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
        $this->assertEquals(Load::TYPE, $actualResult);
    }

    /**
     * @return array[]
     */
    public function provideEspeak()
    {
        return [
            ['12', "12.0"],
            ['12.22', "12.2"],
            [12.22, "12.2"],
            [12.9999, "13.0"],
        ];
    }

    /**
     * @return array[]
     */
    public function provideValues()
    {

        return [
            ['12', 12.0],
            ['12.22', 12.2],
            [12.22, 12.2],
            [12.9999, 13.0],
        ];
    }
}
