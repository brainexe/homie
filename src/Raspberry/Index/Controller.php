<?php

namespace Raspberry\Index;

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

    use TwigTrait;

    /**
     * @param Request $request
     * @return Response
     * @Route("/", name="index")
     * @Guest
     */
    public function index(Request $request)
    {
        $response = $this->renderToResponse('layout.html.twig', [
            'current_user' => $request->attributes->get('user')
        ]);

        return $response;
    }
}
