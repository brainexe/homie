<?php

namespace Tests\Homie\Display\Devices;

use Homie\Display\Devices\EchoDevice;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Display\Devices\EchoDevice
 */
class GatewayTest extends TestCase
{

    /**
     * @var EchoDevice
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new EchoDevice();
    }

    public function testOutput()
    {
        $string = "foo\nbar";

        $this->expectOutputString($string);

        $this->subject->display($string);
    }
}
