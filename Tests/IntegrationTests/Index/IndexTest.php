<?php

namespace IntegrationTests\Index;

use IntegrationTests\RequestTest;
use Symfony\Component\HttpFoundation\Request;

class IndexTest extends RequestTest
{
    public function testIndex()
    {
        $request = new Request();

        $response = $this->handleRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('DOCTYPE html', $response->getContent());
    }

    public function test404()
    {
        $request = new Request();
        $request->server->set('REQUEST_URI', '/notexistingloremipsum');

        $response = $this->handleRequest($request);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertContains('Page not found: /notexistingloremipsum', $response->getContent());
    }
}
