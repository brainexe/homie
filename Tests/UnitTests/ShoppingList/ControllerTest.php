<?php

namespace Tests\Homie\ShoppingList;

use Homie\ShoppingList\Controller;
use Homie\ShoppingList\ShoppingList;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \Homie\ShoppingList\Controller
 */
class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var ShoppingList|MockObject
     */
    private $shoppingList;


    public function setUp()
    {
        $this->shoppingList = $this->createMock(ShoppingList::class);

        $this->subject = new Controller(
            $this->shoppingList
        );
    }

    public function testIndex()
    {
        $shoppingList = ['shoppingList'];

        $this->shoppingList
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($shoppingList);

        $actual   = $this->subject->index();
        $expected = [
            'shoppingList' => $shoppingList
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testAddItem()
    {
        $name = 'name';

        $request = new Request();
        $request->request->set('name', $name);

        $this->shoppingList
            ->expects($this->once())
            ->method('addItem')
            ->with($name);

        $actual = $this->subject->addItem($request);

        $this->assertTrue($actual);
    }

    public function testRemoveItem()
    {
        $name = 'name';

        $request = new Request();

        $this->shoppingList
            ->expects($this->once())
            ->method('removeItem')
            ->with($name);

        $actual = $this->subject->removeItem($request, $name);

        $this->assertTrue($actual);
    }
}
