<?php

namespace Homie\Gpio;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use Homie\Node\Gateway;
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
     * @var Gateway
     */
    private $nodes;

    /**
     * @param GpioManager $manager
     * @param Gateway $nodes
     */
    public function __construct(GpioManager $manager, Gateway $nodes)
    {
        $this->manager = $manager;
        $this->nodes   = $nodes;
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

        $node = $this->nodes->get($nodeId);
        $pins = $this->manager->getPins($node);

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
        $node = $this->nodes->get($nodeId);

        return $this->manager->setPin($node, $pinId, (bool)$status, (bool)$value);
    }

    /**
     * @param Request $request
     * @Route("/gpio/description/", name="gpio.description", methods="POST")
     * @return bool
     */
    public function setDescription(Request $request) : bool
    {
        $pinId       = $request->request->getInt('pinId');
        $nodeId      = $request->request->getInt('nodeId');
        $description = $request->request->get('description');
        $node        = $this->nodes->get($nodeId);

        $this->manager->setDescription($node, $pinId, $description);

        return true;
    }
}
