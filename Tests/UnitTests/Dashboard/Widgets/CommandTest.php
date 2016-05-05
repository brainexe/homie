<?php

namespace Tests\Homie\Dashboard\Widgets;

use Homie\Dashboard\Widgets\Command;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

/**
 * @covers Homie\Dashboard\Widgets\Command
 */
class CommandTest extends TestCase
{

    /**
     * @var Command
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Command();
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(Command::TYPE, $actualResult);
    }

    public function testSerialize()
    {
        $actual = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVo::class, $actual);
    }

    public function testJsonEncode()
    {
        $actualResult = json_encode($this->subject);
        $this->assertInternalType('string', $actualResult);
    }
}
