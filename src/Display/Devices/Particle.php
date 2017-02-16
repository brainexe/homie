<?php

namespace Homie\Display\Devices;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Display\Annotation\DisplayDevice;
use Homie\Expression\Event\EvaluateEvent;
use Homie\Node;

/**
 * @DisplayDevice
 */
class Particle implements DeviceInterface
{

    const TYPE = 'particle';

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
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

    /**
     * @return string
     */
    public static function getType() : string
    {
        return self::TYPE;
    }
}
