<?php

namespace Homie\Flower;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;

/**
 * @ControllerAnnotation("FlowerController")
 */
class Controller
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
     * @return bool
     */
    public function water()
    {

        // TODO implement arduino API
        return true;
    }
}