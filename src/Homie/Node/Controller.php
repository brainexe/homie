<?php

namespace Homie\Node;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\IdGeneratorTrait;
use Homie\Node;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ControllerAnnotation("Node.Controller")
 */
class Controller
{

    use IdGeneratorTrait;
    /**
     * @var Node
     */
    private $node;

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @Inject({
     *  "@Node",
     *  "@Node.Gateway",
     * })
     * @param Node $node
     * @param Gateway $gateway
     */
    public function __construct(Node $node, Gateway $gateway)
    {
        $this->node    = $node;
        $this->gateway = $gateway;
    }

    /**
     * @return Response
     * @Route("/node/", name="node.index", methods="GET")
     */
    public function index()
    {
        return [
           'nodes'     => $this->gateway->getAll(),
           'currentId' => $this->node->getNodeId()
        ];
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/node/", name="node.add", methods="POST")
     */
    public function add(Request $request)
    {
    }

    /**
     * @param Request $request
     * @param $nodeID
     * @return array
     * @Route("/node/{nodeID}/edit/", name="node.edit", methods="PUT")
     */
    public function edit(Request $request, $nodeID)
    {
    }
}
