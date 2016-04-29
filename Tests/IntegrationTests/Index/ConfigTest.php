<?php

namespace IntegrationTests\Index;

use BrainExe\Core\Application\AppKernel;
use IntegrationTests\RequestTest;
use Symfony\Component\HttpFoundation\Request;

class ConfigTest extends RequestTest
{
    public function testConfig()
    {
        $request = new Request();
        $request->server->set('REQUEST_URI', '/config/');

        /** @var AppKernel $kernel */
        $response = $this->handleRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringStartsWith('{', $response->getContent());
    }
}
