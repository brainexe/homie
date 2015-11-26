<?php

namespace Tests\Homie\Sensors\Sensors\System;

use PHPUnit_Framework_TestCase as TestCase;
use Homie\Sensors\Sensors\System\Load;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

class LoadTest extends TestCase
{

    /**
     * @var Load
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Load();
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(Load::TYPE, $actualResult);
    }

    public function testGetValue()
    {
        $pin = 1;

        $actualResult = $this->subject->getValue($pin);

        $this->assertTrue(is_numeric($actualResult));
    }

    public function testIsSupported()
    {
        $output = new DummyOutput();

        $actual = $this->subject->isSupported('', $output);

        $this->assertTrue($actual);
    }

    public function testSerialize()
    {
        $actual = json_encode($this->subject->jsonSerialize());

        $this->assertEquals('{"name":"Load","type":"load","formatter":"load","neededPackages":null}', $actual);
    }
}
