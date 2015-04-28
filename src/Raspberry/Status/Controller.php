<?php

namespace Raspberry\Status;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Application\SelfUpdate\SelfUpdateEvent;
use BrainExe\MessageQueue\Gateway;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("StatusController")
 */
class Controller
{

    use EventDispatcherTrait;

    /**
     * @var Gateway
     */
    private $Gateway;

    /**
     * @Inject("@MessageQueue.Gateway")
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->Gateway = $gateway;
    }

    /**
     * @Route("/status/", name="status.index")
     */
    public function index()
    {
        return [
            'jobs' => $this->Gateway->getEventsByType(),
            'stats' => [
                'Queue Len' => $this->Gateway->countJobs()
            ],
        ];
    }

    /**
     * @Route("/status/event/delete/", methods="POST")
     * @param Request $request
     * @return boolean
     */
    public function deleteJob(Request $request)
    {
        $jobId = $request->request->get('job_id');
        $this->Gateway->deleteEvent($jobId);

        return true;
    }

    /**
     * @Route("/status/self_update/", name="status.self_update", methods="POST")
     */
    public function startSelfUpdate()
    {
        $event = new SelfUpdateEvent(SelfUpdateEvent::TRIGGER);

        $this->dispatchInBackground($event);

        return true;
    }
}
