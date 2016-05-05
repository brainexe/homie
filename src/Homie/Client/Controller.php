<?php

namespace Homie\Client;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Client.Controller")
 */
class Controller
{

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @Inject("@HomieClient.Local")
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param Request $request
     * @return string
     * @Route("/command/", name="command.execute", methods="POST")
     */
    public function execute(Request $request) : string
    {
        $command = $request->request->get('command');

        return $this->client->executeWithReturn($command);
    }
}
