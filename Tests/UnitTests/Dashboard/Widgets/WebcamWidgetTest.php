<?php

namespace Tests\Homie\Dashboard\Widgets;

use Homie\Dashboard\Widgets\Status;
use Homie\Dashboard\Widgets\Webcam;
use PHPUnit\Framework\TestCase;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

class WebcamWidgetTest extends TestCase
{

    /**
     * @var Status
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Webcam();
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(Webcam::TYPE, $actualResult);
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
