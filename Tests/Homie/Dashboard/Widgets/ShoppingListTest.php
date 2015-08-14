<?php

namespace Tests\Homie\Dashboard\Widgets;

use Homie\Dashboard\Widgets\ShoppingList;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

/**
 * @covers Homie\Dashboard\Widgets\ShoppingList
 */
class ShoppingListTest extends TestCase
{

    /**
     * @var ShoppingList
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new ShoppingList();
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(ShoppingList::TYPE, $actualResult);
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
