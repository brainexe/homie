<?php

namespace Homie\Expression\Listener;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;
use Homie\Expression\Event\EvaluateEvent;
use Homie\Expression\Language;

/**
 * @EventListener("Expression.Listener.Evaluate")
 */
class Evaluate
{
    /**
     * @var Language
     */
    private $language;

    /**
     * @Inject({
     *     "@Expression.Language",
     * })
     * @param Language $language
     */
    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    /**
     * @Listen(EvaluateEvent::EVALUATE)
     * @param EvaluateEvent $event
     */
    public function evaluate(EvaluateEvent $event)
    {
        $this->language->evaluate(
            $event->getExpression(),
            $event->getVariables()
        );
    }
}
