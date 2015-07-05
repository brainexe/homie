<?php

namespace Tests\Homie\Dashboard\Widgets;

use Homie\Dashboard\Widgets\StatusWidget;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

/**
 * @covers Homie\Dashboard\Widgets\StatusWidget
 */
class StatusWidgetTest extends TestCase
{

    /**
     * @var StatusWidget
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new StatusWidget();
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(StatusWidget::TYPE, $actualResult);
    }

    public function testSerialize()
    {
        $actualResult = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVO::class, $actualResult);
    }

    public function testJsonEncode()
    {
        $actualResult = json_encode($this->subject);

        $expectedResult = '{"name":"Status","description":"Show internal information","parameters":[],"widgetId":"status","width":6}';
        $this->assertEquals($expectedResult, $actualResult);
    }
}
