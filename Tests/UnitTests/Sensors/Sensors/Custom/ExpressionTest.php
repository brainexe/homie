<?php

namespace Tests\Homie\Sensors\Sensors\Misc;

use Homie\Expression\Language;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\Misc\Expression;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Sensors\Sensors\Misc\Expression
 */
class ExpressionTest extends TestCase
{

    /**
     * @var Expression
     */
    private $subject;

    /**
     * @var Language|MockObject
     */
    private $language;

    public function setUp()
    {
        $this->language = $this->createMock(Language::class);
        $this->subject  = new Expression($this->language);
    }

    public function testGetValue()
    {
        $parameter = '1 + 2';
        $value = 3;

        $this->language
            ->expects($this->once())
            ->method('evaluate')
            ->with($parameter)
            ->willReturn($value);

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;
        $actual = $this->subject->getValue($sensor);

        $this->assertEquals($value, $actual);
    }

    public function testIsSupported()
    {
        $parameter = '1 + 4';

        $this->language
            ->expects($this->once())
            ->method('evaluate')
            ->with($parameter)
            ->willReturn(5);

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;
        $actual = $this->subject->isSupported($sensor);

        $this->assertTrue($actual);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
