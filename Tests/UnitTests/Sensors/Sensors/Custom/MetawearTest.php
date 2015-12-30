<?php

namespace Tests\Homie\Sensors\Sensors\Misc;

use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\Misc\Metawear;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Sensors\Sensors\Misc\Metawear
 */
class MetawearTest extends TestCase
{

    /**
     * @var Metawear
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Metawear();
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }

    public function testSearch()
    {
        $actual = $this->subject->search();
        $this->assertInternalType('array', $actual);
    }
}
