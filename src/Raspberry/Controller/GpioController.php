<?php

namespace Raspberry\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Controller\ControllerInterface;
use Raspberry\Gpio\GpioManager;
use Raspberry\Gpio\Pin;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class GpioController implements ControllerInterface
{

    /**
     * @var GpioManager;
     */
    private $manager;

    /**
     * @Inject("@GpioManager")
     * @param GpioManager $manager
     */
    public function __construct(GpioManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/gpio/", name="gpio.index");
     * @return array
     */
    public function index()
    {
        $pins = $this->manager->getPins();

        return [
            'pins' => $pins->getAll()
        ];
    }

    /**
     * @param Request $request
     * @param integer $sensorId
     * @param string $status
     * @param integer $value
     * @return Pin
     * @Route("/gpio/set/{id}/{status}/{value}/", name="gpio.set", methods="POST")
     */
    public function setStatus(Request $request, $sensorId, $status, $value)
    {
        unset($request);

        $pin = $this->manager->setPin($sensorId, $status, $value);

        return $pin;
    }
}
