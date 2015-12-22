<?php

namespace Tests\Homie\Dashboard\Widgets\TimeWidget;

use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\Time;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

class TimeWidgetTest extends TestCase
{

    /**
     * @var Time
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Time();
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(Time::TYPE, $actualResult);
    }

    public function testValidate()
    {
        $payload = [];

        $actualResult = $this->subject->validate($payload);
        $this->assertTrue($actualResult);
    }

    public function testCreate()
    {
        $payload = [];

        $this->subject->create($payload);
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
