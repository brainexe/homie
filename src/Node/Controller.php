<?php

namespace Homie\Node;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\IdGeneratorTrait;
use Homie\Node;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation(requirements={"nodeId":"\d+"})
 */
class Controller
{
    use IdGeneratorTrait;

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @var int int
     */
    private $currentId;

    /**
     * @Inject({
     *  "currentNodeId" = "%node.id%"
     * })
     * @param Gateway $gateway
     * @param int $currentNodeId
     */
    public function __construct(
        Gateway $gateway,
        int $currentNodeId
    ) {
        $this->gateway   = $gateway;
        $this->currentId = $currentNodeId;
    }

    /**
     * @return array
     * @Route("/nodes/", name="node.index", methods="GET")
     */
    public function index() : array
    {
        return [
            'nodes'     => array_values($this->gateway->getAll()),
            'currentId' => $this->currentId,
            'types'     => Node::TYPES
        ];
    }

    /**
     * @param Request $request
     * @return Node
     * @Route("/nodes/", name="node.add", methods="POST")
     */
    public function add(Request $request) : Node
    {
        $nodeId  = $this->generateUniqueId();
        $type    = $request->request->get('type');
        $options = $this->getOptions($request);
        $name    = $request->request->get('name', '');

        $node = new Node($nodeId, $type, $name, $options);

        $this->gateway->save($node);

        return $node;
    }

    /**
     * @param Request $request
     * @param int $nodeId
     * @return Node
     * @Route("/nodes/{nodeId}/", name="node.edit", methods="PUT")
     */
    public function edit(Request $request, int $nodeId) : Node
    {
        $options = $this->getOptions($request);
        $name    = $request->request->get('name');

        $node = $this->gateway->get($nodeId);
        $node->setOptions($options);
        $node->setName($name);

        $this->gateway->save($node);

        return $node;
    }

    /**
     * @param Request $request
     * @param int $nodeId
     * @return bool
     * @Route("/nodes/{nodeId}/", name="node.delete", methods="DELETE")
     */
    public function delete(Request $request, int $nodeId) : bool
    {
        unset($request);

        return $this->gateway->delete($nodeId);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getOptions(Request $request) : array
    {
        return (array)json_decode($request->request->get('options'), true);
    }
}
