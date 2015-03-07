<?php

namespace Tests\Raspberry\Sensors\Sensors;

use PHPUnit_Framework_TestCase;

use Raspberry\Sensors\Sensors\Load;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @Covers Raspberry\Sensors\Sensors\Load
 */
class LoadTest extends PHPUnit_Framework_TestCase
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

        $actualResult = $this->subject->isSupported('', $output);

        $this->assertTrue($actualResult);
    }
}
