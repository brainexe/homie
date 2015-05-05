<?php

namespace Homie\Index;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\TwigTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ControllerAnnotation("IndexController")
 */
class Controller
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/", name="index")
     * @Guest
     */
    public function index(Request $request)
    {
        $response = file_get_contents(ROOT . '/web/index.html');

        return new Response($response);
    }
}
