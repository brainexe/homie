<?php

namespace IntegrationTests;

use BrainExe\Core\Application\AppKernel;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

class RequestTest extends TestCase
{
    public function testTest()
    {
        $this->markTestIncomplete("TODO implement integration tests");

        /** @var Container $dic */
        global $dic;

        $request = new Request();

        /** @var AppKernel $kernel */
        $kernel   = $dic->get('AppKernel');
        $response = $kernel->handle($request);

        print_r($response);
    }
}
