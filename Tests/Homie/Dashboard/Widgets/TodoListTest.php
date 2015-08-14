<?php

namespace Tests\Homie\Dashboard\Widgets;

use Homie\Dashboard\Widgets\TodoList;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

/**
 * @covers Homie\Dashboard\Widgets\TodoList
 */
class TodoListTest extends TestCase
{

    /**
     * @var TodoList
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new TodoList();
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(TodoList::TYPE, $actualResult);
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
