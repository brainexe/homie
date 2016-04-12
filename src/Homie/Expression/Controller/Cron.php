<?php

namespace Homie\Expression\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\EventDispatcher\Events\TimingEvent;
use BrainExe\Core\EventDispatcher\CronEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Expression.Controller.Cron")
 */
class Cron
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @Inject({
     *  "@EventDispatcher",
     * })
     * @param EventDispatcher $dispatcher
     */
    public function __construct(
        EventDispatcher $dispatcher
    ) {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Request $request
     * @Route("/expressions/cron/", name="expressions.cron")
     * @return bool
     */
    public function addCron(Request $request) : bool
    {
        $cronExpression = $request->request->get('expression');
        $cronId         = $request->request->get('cronId');

        $event = new CronEvent(
            new TimingEvent($cronId),
            $cronExpression
        );

        $this->dispatcher->dispatchEvent($event);

        return true;
    }
}
