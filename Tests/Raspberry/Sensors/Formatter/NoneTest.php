<?php

namespace Tests\Raspberry\Sensors\Formatter;

use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Sensors\Formatter\None;

/**
 * @Covers Raspberry\Sensors\Formatter\None
 */
class NoneTest extends TestCase
{

    /**
     * @var None
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new None();
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
        $this->assertEquals(None::TYPE, $actualResult);
    }

    /**
     * @return array[]
     */
    public function provideEspeak()
    {
        return [
            ['12', "12"],
            ['12.22', "12.22"],
            [12.22, "12.22"],
            [12.9999, "12.9999"],
        ];
    }

    /**
     * @return array[]
     */
    public function provideValues()
    {

        return [
            ['12', "12"],
            ['12.22', "12.22"],
            [12.22, "12.22"],
            [12.9999, "12.9999"],
        ];
    }
}
