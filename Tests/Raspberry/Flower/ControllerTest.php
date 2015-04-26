<?php

namespace Tests\Raspberry\Flower;

use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Flower\Controller;

/**
 * @covers Raspberry\Flower\Controller
 */
class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Controller();
    }

    public function testIndex()
    {
        $actualResult = $this->subject->index();

        $this->assertInternalType('integer', $actualResult['humidity']);
    }
    public function testWater()
    {
        $actualResult = $this->subject->water();

        $this->assertTrue($actualResult);
    }
}
