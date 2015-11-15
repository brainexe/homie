<?php

namespace IntegrationTests;

use BrainExe\Core\Application\AppKernel;
use Monolog\Handler\FilterHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

class RequestTest extends TestCase
{

    public function testIndex()
    {
        $dic = $this->bootstrap();

        $request = new Request();

        /** @var AppKernel $kernel */
        $kernel   = $dic->get('AppKernel');
        $response = $kernel->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('DOCTYPE html', $response->getContent());
    }

    public function test404()
    {
        $dic = $this->bootstrap();

        $request = new Request();
        $request->server->set('REQUEST_URI', '/notexistingloremipsum');

        /** @var AppKernel $kernel */
        $kernel   = $dic->get('AppKernel');
        $response = $kernel->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertContains('Page not found: /notexistingloremipsum', $response->getContent());
    }

    /**
     * @return Container
     */
    private function bootstrap()
    {
        /** @var Container $dic */
        global $dic;

        $dic->set('logger', new Logger('', [new TestHandler()]));
        @session_start();

        return $dic;
    }
}
