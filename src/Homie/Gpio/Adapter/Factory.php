<?php

namespace Homie\Gpio\Adapter;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Homie\Gpio\Adapter;
use Homie\Node;
use InvalidArgumentException;

/**
 * Class Factory
 * @package Homie\Gpio\Adapter
 * @Service("Gpio.Adapter.Factory", public=false)
 */
class Factory
{

    /**
     * @var Raspberry
     */
    private $raspberry;

    /**
     * @var Arduino
     */
    private $arduino;

    /**
     * @param Raspberry $raspberry
     * @param Arduino $arduino
     * @Inject({
     *     "@Gpio.Adapter.Raspberry",
     *     "@Gpio.Adapter.Arduino"
     * })
     */
    public function __construct(Raspberry $raspberry, Arduino $arduino)
    {
        $this->raspberry = $raspberry;
        $this->arduino   = $arduino;
    }

    /**
     * @param Node $node
     * @return Adapter
     * @throws InvalidArgumentException
     */
    public function getForNode(Node $node) : Adapter
    {
        switch ($node->getType()) {
            case Node::TYPE_RASPBERRY:
                return $this->raspberry;
            case Node::TYPE_ARDUINO:
                return $this->arduino;
            default:
                throw new InvalidArgumentException(sprintf('Invalid type: %s', $node->getType()));
        }
    }
}
