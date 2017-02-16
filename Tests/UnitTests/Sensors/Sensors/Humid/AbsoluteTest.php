<?php

namespace Tests\Homie\Sensors\Sensors\Humid;

use Homie\Expression\Language;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\Humid\Absolute;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Homie\Sensors\Sensors\Humid\Absolute
 */
class AbsoluteTest extends TestCase
{

    /**
     * @var Absolute
     */
    private $subject;

    /**
     * @var Language|MockObject
     */
    private $language;

    public function setUp()
    {
        $this->language = $this->createMock(Language::class);
        $this->subject  = new Absolute($this->language);
    }

    public function testGetValue()
    {
        $parameter = '1:2';
        $value = 3;

        $this->language
            ->expects($this->once())
            ->method('evaluate')
            ->willReturn($value);

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;
        $actual = $this->subject->getValue($sensor);

        $this->assertEquals($value, $actual);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
