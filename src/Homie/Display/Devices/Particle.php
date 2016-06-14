<?php

namespace Homie\Display\Devices;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Expression\Event\EvaluateEvent;
use Homie\Node;

/**
 * @Service("Display.Devices.Particle", public=false)
 */
class Particle implements DeviceInterface
{

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @Inject("@EventDispatcher")
     * @param EventDispatcher $dispatcher
     */
    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Node $node
     * @param string $content
     */
    public function display(Node $node, string $content)
    {
        $function = $node->getOption('displayFunction');

        $call = sprintf(
            'callParticleFunction(%s, "%s", "%s")',
            $node->getNodeId(),
            $function,
            $content
        );

        $event = new EvaluateEvent($call);

        $this->dispatcher->dispatchInBackground($event);
    }
}
