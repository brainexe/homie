<?php

namespace IntegrationTests\Index;

use BrainExe\Core\Authentication\UserVO;
use IntegrationTests\RequestTest;
use Symfony\Component\HttpFoundation\Request;

class SensorsTest extends RequestTest
{
    public function testNotLoggedIn()
    {
        $request = new Request();
        $request->server->set('REQUEST_URI', '/sensors/load/0/');

        $response = $this->handleRequest($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testIndex()
    {
        $user = new UserVO();
        $user->id = 1;

        $request = new Request();
        $request->server->set('REQUEST_URI', '/sensors/load/0/');

        $this->initUser($request);
        $response = $this->handleRequest($request);

        $body = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertGreaterThan(0, $body['from']);
        $this->assertGreaterThan(0, $body['to']);
        $this->assertGreaterThan(0, $body['ago']);
        $this->assertGreaterThan($body['from'], $body['to']);
    }
}
