<?php

namespace Homie\Display\Devices;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Display\Annotation\DisplayDevice;
use Homie\Expression\Event\EvaluateEvent;
use Homie\Node;

/**
 * @DisplayDevice
 */
class Expression implements DeviceInterface
{

    const TYPE = 'expression';

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
