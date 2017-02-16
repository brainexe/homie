<?php

namespace Tests\Homie\Client;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Client\Controller;
use Homie\Client\Adapter\LocalClient;
use Symfony\Component\HttpFoundation\Request;

class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var LocalClient|MockObject
     */
    private $client;

    public function setUp()
    {
        $this->client = $this->createMock(LocalClient::class);

        $this->subject = new Controller($this->client);
    }

    public function testExecute()
    {
        $command = 'myCommand';
        $output  = 'myOutput';

        $request = new Request();
        $request->request->set('command', $command);

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with($command)
            ->willReturn($output);

        $actual = $this->subject->execute($request);

        $this->assertEquals($output, $actual);
    }
}
