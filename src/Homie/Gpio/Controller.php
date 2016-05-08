<?php

namespace Homie\Gpio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Gpio.Controller")
 */
class Controller
{

    /**
     * @var GpioManager;
     */
    private $manager;

    /**
     * @Inject("@Gpio.GpioManager")
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
    public function index() : array
    {
        $pins = $this->manager->getPins();

        return [
            'pins' => $pins->getAll(),
            'type' => $pins->getType()
        ];
    }

    /**
     * @param Request $request
     * @param int $pinId
     * @param int $status
     * @param int $value
     * @return Pin
     * @Route("/gpio/set/{id}/{status}/{value}/", name="gpio.set", methods="POST")
     */
    public function setStatus(Request $request, int $pinId, int $status, int $value) : Pin
    {
        unset($request);

        $pin = $this->manager->setPin($pinId, (bool)$status, (bool)$value);

        return $pin;
    }

    /**
     * @param Request $request
     * @Route("/gpio/description/", name="gpio.description", methods="POST")
     * @return bool
     */
    public function setDescription(Request $request) : bool
    {
        $pinId       = $request->request->get('pinId');
        $description = $request->request->get('description');

        $this->manager->setDescription($pinId, $description);

        return true;
    }
}
