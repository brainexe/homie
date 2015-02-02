<?php

namespace Raspberry\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Application\SelfUpdate\SelfUpdateEvent;

use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\MessageQueue\MessageQueueGateway;
use BrainExe\Core\Traits\EventDispatcherTrait;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class StatusController implements ControllerInterface
{

    use EventDispatcherTrait;

    /**
     * @var MessageQueueGateway
     */
    private $messageQueueGateway;

    /**
     * @Inject("@MessageQueueGateway")
     * @param MessageQueueGateway $gateway
     */
    public function __construct(MessageQueueGateway $gateway)
    {
        $this->messageQueueGateway = $gateway;
    }

    /**
     * @Route("/status/", name="status.index")
     */
    public function index()
    {
        return [
            'jobs' => $this->messageQueueGateway->getEventsByType(),
            'stats' => [
                'Queue Len' => $this->messageQueueGateway->countJobs()
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
        $this->messageQueueGateway->deleteEvent($jobId);

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
