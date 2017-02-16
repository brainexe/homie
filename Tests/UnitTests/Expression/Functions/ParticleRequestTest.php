<?php

namespace Tests\Homie\Expression\Functions;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\BufferStream;
use GuzzleHttp\Psr7\Response;
use Homie\Expression\Functions\ParticleRequest;
use Homie\Node;
use Homie\Node\Gateway;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * @covers \Homie\Expression\Functions\ParticleRequest
 */
class ParticleRequestTest extends TestCase
{

    /**
     * @var ParticleRequest
     */
    private $subject;

    /**
     * @var Client|MockObject
     */
    private $client;

    /**
     * @var Gateway|MockObject
     */
    private $nodes;

    public function setUp()
    {
        $this->client = $this->createMock(Client::class);
        $this->nodes  = $this->createMock(Gateway::class);

        $this->subject = new ParticleRequest(
            $this->client,
            $this->nodes
        );
    }

    public function testEvaluatorWithError()
    {
        $node = new Node(1212, Node::TYPE_PARTICLE);
        $node->setOptions([
            'accessToken' => $accessToke = 'myAccessToken',
            'deviceId'    => $deviceId   = 'myDeviceId',
        ]);

        $this->nodes
            ->expects($this->once())
            ->method('get')
            ->with(1212)
            ->willReturn($node);

        $this->client
            ->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://api.particle.io/v1/devices/myDeviceId/myFunction?access_token=myAccessToken&format=raw&args=args'
            )
            ->willThrowException(new Exception('myException'));

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $actual = $evaluator([], 1212, 'myFunction', 'args');

        $this->assertEquals('myException', $actual);
    }

    public function testEvaluator()
    {
        $node = new Node(1212, Node::TYPE_PARTICLE);
        $node->setOptions([
            'accessToken' => $accessToke = 'myAccessToken',
            'deviceId'    => $deviceId   = 'myDeviceId',
        ]);

        $body = new BufferStream();
        $body->write('18150');

        $response = new Response();
        $response = $response->withBody($body);

        $this->nodes
            ->expects($this->once())
            ->method('get')
            ->with(1212)
            ->willReturn($node);

        $this->client
            ->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://api.particle.io/v1/devices/myDeviceId/myFunction?access_token=myAccessToken&format=raw&args=args'
            )
            ->willReturn($response);

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $actual = $evaluator([], 1212, 'myFunction', 'args');

        $this->assertEquals('18150', $actual);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Function "callParticleFunction" is not allowed as trigger
     */
    public function testCompiler()
    {
        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];

        $compiler = $function->getCompiler();
        $compiler();
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Function "getParticleFunction" is not allowed as trigger
     */
    public function testGetParticleFunctionCompiler()
    {
        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[1];

        $compiler = $function->getCompiler();
        $compiler();
    }
}
