<?php

namespace IntegrationTests;

use BrainExe\Core\Application\AppKernel;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class RequestTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @return Container
     */
    protected function setUp()
    {
        /** @var Container $dic */
        global $dic;

        $dic->set('logger', new Logger('', [new TestHandler()]));
        @session_start();

        $this->container = $dic;
    }

    /**
     * @param Request $request
     * @return Response
     */
    protected function handleRequest(Request $request) : Response
    {
        /** @var AppKernel $kernel */
        $kernel   = $this->getContainer()->get('AppKernel');

        return $kernel->handle($request);
    }

    /**
     * @return Container
     */
    protected function getContainer() : Container
    {
        return $this->container;
    }
}
