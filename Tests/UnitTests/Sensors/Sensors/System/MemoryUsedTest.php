<?php

namespace Tests\Homie\Sensors\Sensors\System;

use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\System\MemoryUsed;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @covers Homie\Sensors\Sensors\System\MemoryUsed
 */
class MemoryUsedTest extends TestCase
{

    /**
     * @var MemoryUsed
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new MemoryUsed();
    }

    public function testIsSupported()
    {
        $output = new DummyOutput();
        $sensor = new SensorVO();

        $actual = $this->subject->isSupported($sensor, $output);
        $this->assertTrue($actual);
    }

    public function testGetValue()
    {
        $sensor = new SensorVO();
        $actual = $this->subject->getValue($sensor);
        $this->assertInternalType('int', $actual);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
