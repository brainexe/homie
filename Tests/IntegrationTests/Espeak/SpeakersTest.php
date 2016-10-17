<?php

namespace IntegrationTests\Espeak;

use IntegrationTests\RequestTest;
use Symfony\Component\HttpFoundation\Request;

class SpeakersTest extends RequestTest
{
    public function testGetSpeakers()
    {
        $request = new Request();
        $request->server->set('REQUEST_URI', '/espeak/speakers/');

        $this->initUser($request);
        $response = $this->handleRequest($request);

        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
    }
}
