<?php

namespace Tests\Homie\Dashboard\Widgets;

use Homie\Dashboard\Widgets\Status;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

class StatusWidgetTest extends TestCase
{

    /**
     * @var Status
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Status();
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(Status::TYPE, $actualResult);
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
