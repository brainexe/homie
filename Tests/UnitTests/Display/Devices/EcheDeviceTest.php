<?php

namespace Tests\Homie\Display\Devices;

use Homie\Display\Devices\EchoDevice;
use Homie\Node;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Homie\Display\Devices\EchoDevice
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

        $node = new Node(1, Node::TYPE_SERVER);

        $this->expectOutputString($string);

        $this->subject->display($node, $string);
    }
}
