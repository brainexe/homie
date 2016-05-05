<?php

namespace Tests\Homie\Dashboard\Widgets;

use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\EggTimer;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

class EggTimerWidgetTest extends TestCase
{

    /**
     * @var EggTimer
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new EggTimer();
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(EggTimer::TYPE, $actualResult);
    }

    public function testSerialize()
    {
        $actualResult = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVo::class, $actualResult);
    }

    public function testJsonEncode()
    {
        $actualResult = json_encode($this->subject);
        $this->assertInternalType('string', $actualResult);
    }
}
