<?php

namespace Tests\Homie\Dashboard\Widgets;

use Generator;
use Homie\Espeak\Speakers;
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
     * @var Speakers|MockObject
     */
    private $speakers;

    public function setUp()
    {
        $this->speakers = $this->getMockWithoutInvokingTheOriginalConstructor(Speakers::class);
        $this->subject  = new Speak($this->speakers);
    }

    public function testGetId()
    {
        $actual = $this->subject->getId();
        $this->assertEquals(Speak::TYPE, $actual);
    }

    public function testSerialize()
    {
        $actual = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVo::class, $actual);
    }

    public function testJsonEncode()
    {
        $actual = json_encode($this->subject);
        $this->assertInternalType('string', $actual);
    }

    public function testGetTokens()
    {
        $actual = $this->subject->getTokens();
        $this->assertInstanceOf(Generator::class, $actual);
    }
}
