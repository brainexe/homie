<?php

namespace Tests\Homie\Radio\Change;

use Homie\Radio\Change\Radio;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Client\LocalClient;
use Homie\Radio\Gateway;
use Homie\Radio\VO\RadioVO;

/**
 * @covers Homie\Radio\Change\Radio
 */
class RadioTest extends TestCase
{

    /**
     * @var Radio
     */
    private $subject;

    /**
     * @var LocalClient|MockObject
     */
    private $client;

    /**
     * @var string
     */
    private $rcSwitchCommand;

    public function setUp()
    {
        $this->rcSwitchCommand = '/opt/rc_switch';
        $this->client          = $this->getMock(LocalClient::class, [], [], '', false);
        $this->subject         = new Radio(
            $this->client,
            $this->rcSwitchCommand
        );
    }

    public function testSetStatus()
    {
        $radioVo = new RadioVO();
        $radioVo->code = "0101";
        $radioVo->pin  = 2;
        $status = 1;

        $this->client
            ->expects($this->once())
            ->method('execute')
            ->with("/opt/rc_switch '0101' 2 1");

        $this->subject->setStatus($radioVo, $status);
    }
}
