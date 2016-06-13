<?php

namespace Tests\Homie\Expression\Functions;

use GuzzleHttp\Client;
use Homie\Expression\Functions\WebserviceRequest;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * @covers Homie\Expression\Functions\WebserviceRequest
 */
class WebserviceRequestTest extends TestCase
{

    /**
     * @var WebserviceRequest
     */
    private $subject;

    /**
     * @var Client|MockObject
     */
    private $client;

    public function setUp()
    {
        $this->client = $this->createMock(Client::class);

        $this->subject = new WebserviceRequest($this->client);
    }

    public function testEvaluator()
    {
        $url = 'http://example.com';

        $this->client
            ->expects($this->once())
            ->method('request')
            ->with('POST', $url, ['args'])
            ->willReturn(['result']);

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $actual = $evaluator([], $url, 'POST', ['args']);

        $this->assertEquals(['result'], $actual);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Function "webserviceRequest" is not allowed as trigger
     */
    public function testCompiler()
    {
        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];

        $compiler = $function->getCompiler();
        $compiler();
    }
}
