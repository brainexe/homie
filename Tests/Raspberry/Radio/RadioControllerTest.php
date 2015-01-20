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

    public function setUp()
    {
        $this->mockLocalClient = $this->getMock(LocalClient::class, [], [], '', false);
        $this->subject = new RadioController($this->mockLocalClient);
    }

    public function testSetStatus()
    {
        $code = 0;
        $number = 1;
        $status = 1;

        $this->mockLocalClient
            ->expects($this->once())
            ->method('execute')
            ->with($this->anything());

        $this->subject->setStatus($code, $number, $status);
    }
}
