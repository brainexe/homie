<?php

namespace Tests\Raspberry\Radio;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Radio\RadioController;
use Raspberry\Client\LocalClient;
use Raspberry\Radio\RadioGateway;
use Raspberry\Radio\VO\RadioVO;

/**
 * @covers Raspberry\Radio\RadioController
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
    private $client;

    /**
     * @var string
     */
    private $rcSwitchCommand;

    /**
     * @var RadioGateway|MockObject
     */
    private $gateway;

    public function setUp()
    {
        $this->rcSwitchCommand = '/opt/rc_switch';
        $this->client          = $this->getMock(LocalClient::class, [], [], '', false);
        $this->gateway         = $this->getMock(RadioGateway::class, [], [], '', false);
        $this->subject         = new RadioController($this->client, $this->gateway, $this->rcSwitchCommand);
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
            ->with('/opt/rc_switch 0101 2 1');

        $this->client
            ->expects($this->once())
            ->method('execute')
            ->with('/opt/rc_switch 0101 2 1');

        $this->subject->setStatus($radioVo, $status);
    }
}
