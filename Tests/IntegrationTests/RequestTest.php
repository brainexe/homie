<?php

namespace IntegrationTests;

use BrainExe\Core\Application\AppKernel;
use BrainExe\Core\Authentication\UserVO;
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

    /**
     * @param Request $request
     * @param UserVO|null $user
     * @return UserVO
     */
    protected function initUser(Request $request, UserVO $user = null) : UserVO
    {
        if (empty($user)) {
            $user     = new UserVO();
            $user->id = rand(1, 1000000);
        }

        $request->attributes->set('user_id', $user->id);
        $request->attributes->set('user', $user);

        return $user;
    }
}
