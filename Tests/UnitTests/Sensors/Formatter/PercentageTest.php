<?php

namespace Tests\Homie\Sensors\Formatter;

use PHPUnit\Framework\TestCase;
use Homie\Sensors\Formatter\Percentage;

/**
 * @covers \Homie\Sensors\Formatter\Percentage
 */
class PercentageTest extends TestCase
{

    /**
     * @var Percentage
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Percentage();
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
        $this->assertEquals(Percentage::TYPE, $actualResult);
    }

    /**
     * @return array[]
     */
    public function provideValues()
    {
        return [
            ['12', "12%"],
            ['12.22', "12%"],
            [12.22, "12%"],
            [12.9999, "12%"],
        ];
    }
}
