<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\MessageQueue\MessageQueueGateway;
use Matze\Core\Traits\RedisTrait;
use Matze\Core\Traits\TwigTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @Controller
 */
class StatusController extends AbstractController {

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
	 * @Route("/status/event/delete/{job_id}/")
	 * @param Request $request
	 * @param string $job_id
	 * @return RedirectResponse
	 */
	public function deleteJob(Request $request, $job_id) {
		$this->_message_queue_gateway->deleteEvent($job_id);

		return new RedirectResponse('/status/');
	}

}
