<?php

namespace Raspberry\Flower;

use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Controller\ControllerInterface;

/**
 * @Controller
 */
class FlowerController implements ControllerInterface
{

    /**
     * @Route("/flower/", name="flower.index");
     * @return array
     */
    public function index()
    {
        return [
            'humidity' => rand(20, 80) // TODO implement arduino API
        ];
    }

    /**
     * @Route("/flower/water/", name="flower.water", methods="POST");
     * @return array
     */
    public function water()
    {

        // TODO implement arduino API
        return true;
    }
}
