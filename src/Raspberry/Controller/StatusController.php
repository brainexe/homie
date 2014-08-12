<?php

namespace Raspberry\Controller;

use Matze\Core\Application\SelfUpdate\SelfUpdateEvent;
use Matze\Core\Controller\AbstractController;
use Matze\Core\MessageQueue\MessageQueueGateway;
use Matze\Core\Traits\EventDispatcherTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class StatusController extends AbstractController {

	use EventDispatcherTrait;

	/**
	 * @var MessageQueueGateway
	 */
	private $_message_queue_gateway;

	/**
	 * @Inject("@MessageQueueGateway")
	 * @param MessageQueueGateway $message_queue_gateway
	 */
	public function __construct(MessageQueueGateway $message_queue_gateway) {
		$this->_message_queue_gateway = $message_queue_gateway;
	}

	/**
	 * @Route("/status/", name="status.index")
	 */
	public function index() {
		return new JsonResponse([
			'jobs' => $this->_message_queue_gateway->getEventsByType(),
			'stats' => [
				'Queue Len' => $this->_message_queue_gateway->countJobs()
			],
		]);
	}

	/**
	 * @Route("/status/event/delete/", methods="POST")
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function deleteJob(Request $request) {
		$job_id = $request->request->get('job_id');
		$this->_message_queue_gateway->deleteEvent($job_id);

		return new JsonResponse(true);
	}

	/**
	 * @Route("/status/self_update/", name="status.self_update", methods="POST")
	 */
	public function startSelfUpdate() {
		$event = new SelfUpdateEvent(SelfUpdateEvent::TRIGGER);

		$this->dispatchInBackground($event);

		return new JsonResponse(true);
	}
}
