<?php

namespace Tests\Raspberry\Sensors\Sensors;

use PHPUnit_Framework_TestCase;

use Raspberry\Sensors\Sensors\Load;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @covers Raspberry\Sensors\Sensors\Load
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

    public function testIsSupported()
    {
        $output = new DummyOutput();

        $actualResult = $this->subject->isSupported('', $output);

        $this->assertTrue($actualResult);
    }
}
