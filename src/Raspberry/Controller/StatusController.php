<?php

namespace Raspberry\Controller;

use Matze\Core\Application\SelfUpdate\SelfUpdateEvent;
use Matze\Core\Controller\AbstractController;
use Matze\Core\MessageQueue\MessageQueueGateway;
use Matze\Core\Traits\EventDispatcherTrait;
use Matze\Core\Traits\RedisTrait;
use Matze\Core\Traits\TwigTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
	 */
	public function __construct(MessageQueueGateway $message_queue_gateway) {
		$this->_message_queue_gateway = $message_queue_gateway;
	}

	/**
	 * @Route("/status/", name="status.index")
	 */
	public function index() {
		return $this->render('status.html.twig', [
			'jobs' => $this->_message_queue_gateway->getEventsByType(),
			'stats' => [
				'Queue Len' => $this->_message_queue_gateway->countJobs()
			],
		]);
	}

	/**
	 * @Route("/status/event/delete/{job_id}/", csrf=true)
	 * @param Request $request
	 * @param string $job_id
	 * @return RedirectResponse
	 */
	public function deleteJob(Request $request, $job_id) {
		$this->_message_queue_gateway->deleteEvent($job_id);

		return new RedirectResponse('/status/');
	}

	/**
	 * @Route("/status/self_update/", name="status.self_update", csrf=true)
	 * @return RedirectResponse
	 */
	public function startSelfUpdate() {
		$event = new SelfUpdateEvent(SelfUpdateEvent::TRIGGER);

		$this->dispatchInBackground($event);

		return new RedirectResponse('/status/');
	}
}
