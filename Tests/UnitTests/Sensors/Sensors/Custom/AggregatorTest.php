<?php

namespace Tests\Homie\Sensors\Sensors\Misc;

use Homie\Sensors\Definition;
use Homie\Sensors\Aggregate\Aggregated;
use Homie\Sensors\Sensors\Misc\Aggregator;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Sensors\Sensors\Misc\Aggregator
 */
class AggregatorTest extends TestCase
{

    /**
     * @var Aggregator
     */
    private $subject;

    /**
     * @var Aggregated|MockObject
     */
    private $aggregated;

    public function setUp()
    {
        $this->aggregated = $this->createMock(Aggregated::class);
        $this->subject    = new Aggregator($this->aggregated);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }

    public function testIsSupported()
    {
        $parameter = '12';

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;
        $actual = $this->subject->isSupported($sensor);
        $this->assertTrue($actual);
    }

    public function testGetValue()
    {
        $value = 122;

        $sensor = new SensorVO();
        $sensor->parameter = 'parameter';

        $this->aggregated
            ->expects($this->once())
            ->method('getCurrent')
            ->with('parameter')
            ->willReturn($value);

        $actual = $this->subject->getValue($sensor);

        $this->assertEquals($value, $actual);
    }
}
