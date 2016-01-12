<?php

namespace Homie\Switches;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Annotations\InputControlInterface;
use BrainExe\InputControl\Event;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;
use Generator;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @InputControlAnnotation(name="switch", tags={{"name"="expression_language"}})
 */
class InputControl implements InputControlInterface, ExpressionFunctionProviderInterface
{

    use EventDispatcherTrait;

    /**
     * @var Switches
     */
    private $switches;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            '/^radio (on|off) (\s+)$/i' => 'setSwitch',
            '/^switch (on|off) (\s+)$/i' => 'setSwitch',
        ];
    }

    /**
     * @Inject("@Switches.Switches")
     * @param Switches $switches
     */
    public function __construct(Switches $switches)
    {
        $this->switches = $switches;
    }

    /**
     * @param Event $event
     */
    public function setSwitch(Event $event)
    {
        list ($status, $switchId) = $event->matches;

        $status = $status === 'on';

        $switch = $this->switches->get($switchId);

        $event = new SwitchChangeEvent($switch, $status);
        $this->dispatchInBackground($event);
    }

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('setSwitch', function ($switchId, $status) {
            unset($switchId, $status);
            throw new InvalidArgumentException('Function addNotification() not available as condition');
        }, function (array $variables, $switchId, $status) {
            unset($variables);
            $switch = $this->switches->get($switchId);

            $event = new SwitchChangeEvent($switch, $status);

            $this->dispatchInBackground($event);
        });
    }
}
