<?php

namespace Homie\Index;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ControllerAnnotation("IndexController")
 */
class Controller
{
    private $config = [];

    /**
     * @Inject({"%debug%", "%socket.url%"})
     * @param bool $debug
     * @param string $socketUrl
     */
    public function __construct($debug, $socketUrl)
    {
        $this->config['debug'] = $debug;
        $this->config['socketUrl'] = $socketUrl;
    }

    /**
     * @return Response
     * @Route("/", name="index", methods="GET")
     * @Guest
     */
    public function index()
    {
        $response = file_get_contents(ROOT . '/web/index.html');

        return new Response($response);
    }

    /**
     * @return array
     * @Route("/config/", name="config")
     * @Guest
     */
    public function config()
    {
        return $this->config;
    }
}
