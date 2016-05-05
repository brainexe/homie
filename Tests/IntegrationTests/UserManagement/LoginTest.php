<?php

namespace IntegrationTests\Index;

use IntegrationTests\RequestTest;
use Symfony\Component\HttpFoundation\Request;

class LoginTest extends RequestTest
{
    public function testInvalidPassword()
    {
        $request = new Request();
        $request->server->set('REQUEST_URI', '/login/');
        $request->server->set('REQUEST_METHOD', 'POST');
        $request->request->set('username', 'myNotExistingUsername');
        $request->request->set('password', 'myPassword');

        $response = $this->handleRequest($request);

        $expected = 'Username "myNotExistingUsername" does not exist.';

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expected, $response->getContent());
    }
}
