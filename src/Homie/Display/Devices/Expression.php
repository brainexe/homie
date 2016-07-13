<?php

namespace Homie\Display\Devices;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Display\Annotation\DisplayDevice;
use Homie\Expression\Event\EvaluateEvent;
use Homie\Node;

/**
 * @DisplayDevice("Display.Devices.Expression")
 */
class Expression implements DeviceInterface
{

    const TYPE = 'expression';

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

        $event = new EvaluateEvent($function, [
            'content' => $content
        ]);

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
