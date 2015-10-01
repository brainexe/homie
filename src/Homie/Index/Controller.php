<?php

namespace Homie\Index;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ControllerAnnotation("IndexController")
 */
class Controller
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * @Inject({"%debug%", "%socket.url%", "%locales%"})
     * @param bool $debug
     * @param string $socketUrl
     * @param string[] $locales
     */
    public function __construct($debug, $socketUrl, $locales)
    {
        $this->config['debug']     = $debug;
        $this->config['socketUrl'] = $socketUrl;
        $this->config['locales']   = $locales;
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

    /**
     * @return Response
     * @Route("/robots.txt", name="robots.txt")
     * @Guest
     */
    public function robotstxt()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');
        $response->setContent("User-agent: *\nDisallow: /");

        return $response;
    }
}
