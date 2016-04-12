<?php

namespace Homie\Switches;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;

/**
 * @ExpressionLanguageAnnotation(name="Switches.ExpressionLanguage")
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{

    use EventDispatcherTrait;

    /**
     * @var Switches
     */
    private $switches;

    /**
     * @Inject("@Switches.Switches")
     * @param Switches $switches
     */
    public function __construct(Switches $switches)
    {
        $this->switches = $switches;
    }

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('setSwitch', function (int $switchId, bool $status) {
            unset($switchId, $status);
            throw new InvalidArgumentException('Function addNotification() not available as condition');
        }, function (array $variables, int $switchId, bool $status) {
            unset($variables);
            $switch = $this->switches->get($switchId);

            $event = new SwitchChangeEvent($switch, $status);

            $this->dispatchInBackground($event);
        });
    }
}
