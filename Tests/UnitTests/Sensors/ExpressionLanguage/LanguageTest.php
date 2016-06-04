<?php

namespace Tests\Homie\Sensors\ExpressionLanguage;

use Generator;
use Homie\Sensors\ExpressionLanguage\Language;
use Homie\Sensors\SensorGateway;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * @covers Homie\Sensors\ExpressionLanguage\Language
 */
class LanguageTest extends TestCase
{

    /**
     * @var Language
     */
    private $subject;

    /**
     * @var SensorGateway|MockObject
     */
    private $sensorGateway;

    public function setUp()
    {
        $this->sensorGateway = $this->createMock(SensorGateway::class);
        $this->subject = new Language($this->sensorGateway);
    }

    public function testGetFunctions()
    {
        $actual = $this->subject->getFunctions();
        $this->assertInternalType('array', $actual);
    }

    public function testGetSensor()
    {
        $sensorId = 1000;
        $value    = 42;

        $this->sensorGateway
            ->expects($this->once())
            ->method('getSensor')
            ->with($sensorId)
            ->willReturn($sensor = [
                'lastValue' => $value
            ]);

        /** @var ExpressionFunction $function */
        $actual = $this->subject->getFunctions();

        $function = $actual[1];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $actual = $evaluator([], $sensorId);
        $this->assertEquals($sensor, $actual);

        $compiler = $function->getCompiler();
        $actual = $compiler($sensorId);
        $this->assertInternalType('string', $actual);
    }

    public function testGetSensorValue()
    {
        $sensorId = 1000;
        $value    = 42;

        $this->sensorGateway
            ->expects($this->once())
            ->method('getSensor')
            ->with($sensorId)
            ->willReturn([
                'lastValue' => $value
            ]);

        /** @var ExpressionFunction $function */
        $functions = $this->subject->getFunctions();
        $function = $functions[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $actual = $evaluator([], $sensorId);
        $this->assertEquals($value, $actual);

        $compiler = $function->getCompiler();
        $actual = $compiler($sensorId);
        $this->assertInternalType('string', $actual);
    }
}
