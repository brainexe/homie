<?php

namespace Tests\Homie\Sensors\Sensors;

use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\Barometer;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @covers Homie\Sensors\Sensors\Barometer
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

    public function testGetValue()
    {
        $parameter = 'foo';
        $actual    = $this->subject->getValue($parameter);
        $expected  = 0;

        $this->assertEquals($expected, $actual);
    }

    public function testIsSupported()
    {
        $parameter = null;
        $output    = new DummyOutput();
        $actual    = $this->subject->isSupported($parameter, $output);
        $this->assertTrue($actual);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
