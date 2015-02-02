<?php

namespace Raspberry\Controller;

use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\Traits\TwigTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Controller
 */
class IndexController implements ControllerInterface
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
