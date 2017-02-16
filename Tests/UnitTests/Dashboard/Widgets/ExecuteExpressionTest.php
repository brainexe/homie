<?php

namespace Tests\Homie\Dashboard\Widgets;

use Homie\Dashboard\Widgets\ExecuteExpression;
use PHPUnit\Framework\TestCase;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

/**
 * @covers \Homie\Dashboard\Widgets\ExecuteExpression
 */
class ExecuteExpressionTest extends TestCase
{

    /**
     * @var ExecuteExpression
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new ExecuteExpression();
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(ExecuteExpression::TYPE, $actualResult);
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
