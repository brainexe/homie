<?php

namespace Tests\Homie\Sensors\Sensors\Misc;

use Homie\Expression\Language;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\Misc\Particle;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Sensors\Sensors\Misc\Particle
 */
class ParticleTest extends TestCase
{

    /**
     * @var Particle
     */
    private $subject;

    /**
     * @var Language|MockObject
     */
    private $language;

    public function setUp()
    {
        $this->language = $this->createMock(Language::class);
        $this->subject  = new Particle($this->language);
    }

    public function testGetValue()
    {
        $parameter = 'temperature';
        $value = 3;

        $this->language
            ->expects($this->once())
            ->method('evaluate')
            ->with('callParticleFunction(nodeId, "temperature")')
            ->willReturn($value);

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;
        $actual = $this->subject->getValue($sensor);

        $this->assertEquals($value, $actual);
    }

    public function testGetValueBigValue()
    {
        $parameter = 'temperature';

        $this->language
            ->expects($this->once())
            ->method('evaluate')
            ->with('callParticleFunction(nodeId, "temperature")')
            ->willReturn(343000000);

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;
        $actual = $this->subject->getValue($sensor);

        $this->assertEquals(343, $actual);
    }

    /**
     * @expectedException \Homie\Sensors\Exception\InvalidSensorValueException
     * @expectedExceptionMessage Failed request error
     */
    public function testError()
    {
        $parameter = 'temperature';

        $this->language
            ->expects($this->once())
            ->method('evaluate')
            ->with('callParticleFunction(nodeId, "temperature")')
            ->willReturn("Failed request error");

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;

        $this->subject->getValue($sensor);
    }

    public function testIsSupported()
    {
        $sensor = new SensorVO();
        $actual = $this->subject->isSupported($sensor);

        $this->assertTrue($actual);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
