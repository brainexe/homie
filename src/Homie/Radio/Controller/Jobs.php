<?php

namespace Homie\Radio\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Radio\Job;
use Homie\Radio\Switches;
use Homie\Radio\SwitchChangeEvent;

use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Switch.Controller.Jobs")
 */
class Jobs
{
    use EventDispatcherTrait;

    /**
     * @var Switches;
     */
    private $switches;

    /**
     * @var Job
     */
    private $job;

    /**
     * @Inject({
     *     "@Switch.Switches",
     *     "@Switch.Job"
     * })
     * @param Switches $switches
     * @param Job $job
     */
    public function __construct(
        Switches $switches,
        Job $job
    ) {
        $this->switches = $switches;
        $this->job      = $job;
    }

    /**
     * @param Request $request
     * @param integer $switchId
     * @param integer $status
     * @return bool
     * @Route("/switches/{switchId}/status/{status}/", name="switch.job.set_status", methods="POST")
     */
    public function setStatus(Request $request, $switchId, $status)
    {
        unset($request);

        $switch = $this->switches->get($switchId);

        $event = new SwitchChangeEvent($switch, (bool)$status);
        $this->dispatchInBackground($event);

        return true;
    }

    /**
     * @param Request $request
     * @return bool
     * @Route("/switches/jobs/", name="switch.job.add", methods="POST")
     */
    public function addJob(Request $request)
    {
        $switchId    = $request->request->getInt('switchId');
        $status      = (bool)$request->request->getInt('status');
        $timeString  = (string)$request->request->get('time');

        $switch = $this->switches->get($switchId);

        $this->job->addJob($switch, $timeString, $status);

        return true;
    }
}
