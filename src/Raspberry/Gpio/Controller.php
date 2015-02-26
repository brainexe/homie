<?php

namespace Raspberry\Gpio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation
 */
class Controller
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

    /**
     * @param Request $request
     * @Route("/gpio/description/", name="gpio.set", methods="POST")
     * @return bool
     */
    public function setDescription(Request $request)
    {
        $pinId       = $request->request->get('pinId');
        $description = $request->request->get('description');

        $this->manager->setDescription($pinId, $description);

        return true;
    }
}
