<?php

namespace Tests\Homie\Dashboard\Widgets;

use Homie\Dashboard\Widgets\Expression;
use PHPUnit\Framework\TestCase;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

/**
 * @covers \Homie\Dashboard\Widgets\Expression
 */
class ExpressionTest extends TestCase
{

    /**
     * @var Expression
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Expression();
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(Expression::TYPE, $actualResult);
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
