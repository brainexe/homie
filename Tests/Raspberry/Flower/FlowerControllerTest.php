<?php

namespace Tests\Raspberry\Flower;

use PHPUnit_Framework_TestCase;
use Raspberry\Flower\FlowerController;

/**
 * @Covers Raspberry\Flower
 */
class FlowerControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var FlowerController
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new FlowerController();
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
