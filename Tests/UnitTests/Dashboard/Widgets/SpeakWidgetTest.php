<?php

namespace Tests\Homie\Dashboard\Widgets;

use Homie\Espeak\Espeak;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\Speak;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

class SpeakWidgetTest extends TestCase
{

    /**
     * @var Speak
     */
    private $subject;

    /**
     * @var Espeak|MockObject
     */
    private $espeak;

    public function setUp()
    {
        $this->espeak = $this->getMock(Espeak::class, [], [], '', false);
        $this->subject = new Speak($this->espeak);
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(Speak::TYPE, $actualResult);
    }

    public function testSerialize()
    {
        $actualResult = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVO::class, $actualResult);
    }
    public function testJsonEncode()
    {
        $actualResult = json_encode($this->subject);
        $this->assertInternalType('string', $actualResult);
    }
}
