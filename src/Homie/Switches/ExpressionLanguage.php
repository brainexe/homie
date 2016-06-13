<?php

namespace Homie\Switches;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("Switches.ExpressionLanguage")
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
        yield new Action('setSwitch', function (array $variables, int $switchId, bool $status) {
            unset($variables);
            $switch = $this->switches->get($switchId);

            $event = new SwitchChangeEvent($switch, $status);

            $this->dispatchInBackground($event);
        });
    }
}
