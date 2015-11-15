<?php

namespace Tests\Homie\Sensors\ExpressionLanguage;

use Homie\Sensors\ExpressionLanguage\Language;
use Homie\Sensors\SensorGateway;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

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
        $this->sensorGateway = $this->getMock(SensorGateway::class);
        $this->subject = new Language($this->sensorGateway);
    }

    public function testGetFunctions()
    {
        $actual = $this->subject->getFunctions();
        $this->assertInternalType('array', $actual);
    }
}
