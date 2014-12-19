<?php

namespace Raspberry\Controller;

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
     * @param GpioManager $service_gpio_manager
     */
    public function __construct(GpioManager $service_gpio_manager)
    {
        $this->manager = $service_gpio_manager;
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
     * @param integer $id
     * @param string $status
     * @param integer $value
     * @return Pin
     * @Route("/gpio/set/{id}/{status}/{value}/", name="gpio.set", methods="POST")
     */
    public function setStatus(Request $request, $id, $status, $value)
    {
        $pin = $this->manager->setPin($id, $status, $value);

        return $pin;
    }
}
