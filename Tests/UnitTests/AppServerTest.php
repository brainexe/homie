<?php

namespace Tests\Homie\Node;

use BrainExe\Core\Application\AppKernel;
use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\Time;
use BrainExe\Tests\RedisMockTrait;
use Homie\AppServer;
use Homie\Node;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class AppServerTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var AppServer
     */
    private $subject;

    /**
     * @var AppKernel|MockObject
     */
    private $appKernel;

    /**
     * @var Session|MockObject
     */
    private $session;

    /**
     * @var Predis|MockObject
     */
    private $predis;

    /**
     * @var Time|MockObject
     */
    private $time;

    /**
     * @var int
     */
    private $timeout;

    public function setUp()
    {
        $this->appKernel = $this->createMock(AppKernel::class);
        $this->predis    = $this->getRedisMock();
        $this->session   = $this->createMock(Session::class);
        $this->time      = $this->createMock(Time::class);

        $this->timeout = 10;

        $this->subject = new AppServer(
            $this->appKernel,
            $this->predis,
            $this->session,
            $this->timeout
        );
        $this->subject->setTime($this->time);
    }

    public function testEmpty()
    {
        $raw = [];

        $this->predis
            ->expects($this->once())
            ->method('brpop')
            ->with(AppServer::REQUEST, $this->timeout)
            ->willReturn($raw);

        $this->subject->start();
    }

    public function testExpired()
    {
        $raw = [
            'name',
            json_encode([
                'server' => [
                    'REQUEST_TIME_FLOAT' => 0
                ]
            ])
        ];

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn(10000);

        $this->predis
            ->expects($this->at(0))
            ->method('brpop')
            ->with(AppServer::REQUEST, $this->timeout)
            ->willReturn($raw);

        $this->predis
            ->expects($this->at(1))
            ->method('brpop')
            ->with(AppServer::REQUEST, $this->timeout)
            ->willReturn([]);

        $this->subject->start();
    }

    public function testHandle()
    {
        $raw = [
            'name',
            json_encode([
                'server' => [
                    'REQUEST_TIME_FLOAT' => 10000
                ],
                'get' => [],
                'post' => [],
                'request' => [],
                'cookies' => [],
                'headers' => [],
                'files' => [],
                'raw' => '',
                'requestId' => 'requestId',
                'sessionId' => 'mySid'
            ])
        ];

        $response = new JsonResponse(['test' => 1]);

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn(10000);

        $this->predis
            ->expects($this->at(0))
            ->method('brpop')
            ->with(AppServer::REQUEST, $this->timeout)
            ->willReturn($raw);

        $this->predis
            ->expects($this->at(2))
            ->method('brpop')
            ->with(AppServer::REQUEST, $this->timeout)
            ->willReturn([]);

        $this->session
            ->expects($this->once())
            ->method('setId')
            ->with('mySid');

        $expected = '{"requestId":"requestId","sessionId":null,"status":200,"body":"{\"'
            . 'test\":1}","headers":{"cache-control":["no-cache, private"],"content-type":["application\/json"]}}';
        $this->predis
            ->expects($this->once())
            ->method('publish')
            ->with('response:requestId', $expected);

        $this->appKernel
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(Request::class))
            ->willReturn($response);

        $this->subject->start();
    }
}
