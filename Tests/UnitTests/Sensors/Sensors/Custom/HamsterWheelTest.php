<?php

namespace Tests\Homie\Sensors\Sensors\Misc;

use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\Aggregate\Aggregated;
use Homie\Sensors\Sensors\Misc\HamsterWheel;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @covers Homie\Sensors\Sensors\Misc\HamsterWheel
 */
class HamsterWheelTest extends TestCase
{

    /**
     * @var HamsterWheel
     */
    private $subject;

    /**
     * @var Aggregated|MockObject
     */
    private $aggregated;

    public function setUp()
    {
        $this->aggregated = $this->getMock(Aggregated::class, [], [], '', false);
        $this->subject    = new HamsterWheel($this->aggregated);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }

    public function testIsSupported()
    {
        $parameter = '12';
        $output = new DummyOutput();

        $actual = $this->subject->isSupported($parameter, $output);
        $this->assertTrue($actual);
    }
}
