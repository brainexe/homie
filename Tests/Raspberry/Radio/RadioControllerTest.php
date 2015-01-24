<?php

namespace Tests\Raspberry\Radio\RadioController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Radio\RadioController;
use Raspberry\Client\LocalClient;

/**
 * @Covers Raspberry\Radio\RadioController
 */
class RadioControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RadioController
     */
    private $subject;

    /**
     * @var LocalClient|MockObject
     */
    private $mockLocalClient;

    /**
     * @var string
     */
    private $rcSwitchCommand;

    public function setUp()
    {
        $this->rcSwitchCommand = '/opt/rc_switch';
        $this->mockLocalClient = $this->getMock(LocalClient::class, [], [], '', false);
        $this->subject = new RadioController($this->mockLocalClient, $this->rcSwitchCommand);
    }

    public function testSetStatus()
    {
        $code = "0101";
        $number = 2;
        $status = 1;

        $this->mockLocalClient
            ->expects($this->once())
            ->method('execute')
            ->with('/opt/rc_switch 0101 2 1');

        $this->subject->setStatus($code, $number, $status);
    }
}
