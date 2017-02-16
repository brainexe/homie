<?php

namespace Homie\Expression\Controller;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Cron\Expression;
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
     * @var Expression
     */
    private $cron;

    /**
     * @param EventDispatcher $dispatcher
     * @param Expression $cron
     */
    public function __construct(
        EventDispatcher $dispatcher,
        Expression $cron
    ) {
        $this->dispatcher = $dispatcher;
        $this->cron       = $cron;
    }

    /**
     * @param Request $request
     * @Route("/cron/", name="expressions.cron")
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

    /**
     * @param Request $request
     * @Route("/cron/next/", name="expressions.cron.next")
     * @return int
     */
    public function getNextTime(Request $request) : int
    {
        $cronExpression = $request->request->get('expression');

        return $this->cron->getNextRun($cronExpression);
    }
}
