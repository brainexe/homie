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
     * @Route("/gpio/{nodeId}/", name="gpio.index", requirements={"nodeId":"\d+"});
     * @param Request $request
     * @param int $nodeId
     * @return array
     */
    public function index(Request $request, int $nodeId) : array
    {
        unset($request);

        $pins = $this->manager->getPins();

        return [
            'pins' => array_values($pins->getAll()),
            'type' => $pins->getType()
        ];
    }

    /**
     * @param Request $request
     * @param int $nodeId
     * @param int $pinId
     * @param int $status
     * @param int $value
     * @return Pin
     * @Route("/gpio/set/{nodeId}/{id}/{status}/{value}/", name="gpio.set", methods="POST")
     */
    public function setStatus(Request $request, int $nodeId, int $pinId, int $status, int $value) : Pin
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
        $pinId       = $request->request->getInt('pinId');
        $description = $request->request->get('description');

        $this->manager->setDescription($pinId, $description);

        return true;
    }
}
