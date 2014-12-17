<?php

namespace Raspberry\Controller;

use BrainExe\Core\Application\SelfUpdate\SelfUpdateEvent;

use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\MessageQueue\MessageQueueGateway;
use BrainExe\Core\Traits\EventDispatcherTrait;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class StatusController implements ControllerInterface {

	use EventDispatcherTrait;

	/**
	 * @var MessageQueueGateway
	 */
	private $messageQueueGateway;

	/**
	 * @Inject("@MessageQueueGateway")
	 * @param MessageQueueGateway $message_queue_gateway
	 */
	public function __construct(MessageQueueGateway $message_queue_gateway) {
		$this->messageQueueGateway = $message_queue_gateway;
	}

	/**
	 * @Route("/status/", name="status.index")
	 */
	public function index() {
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
	public function deleteJob(Request $request) {
		$job_id = $request->request->get('job_id');
		$this->messageQueueGateway->deleteEvent($job_id);

		return true;
	}

	/**
	 * @Route("/status/self_update/", name="status.self_update", methods="POST")
	 */
	public function startSelfUpdate() {
		$event = new SelfUpdateEvent(SelfUpdateEvent::TRIGGER);

		$this->dispatchInBackground($event);

		return true;
	}
}
