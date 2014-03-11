<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\MessageQueue\MessageQueue;
use Matze\Core\MessageQueue\MessageQueueGateway;
use Matze\Core\Traits\RedisTrait;
use Matze\Core\Traits\TwigTrait;

/**
 * @Controller
 */
class StatusController extends AbstractController {

	use RedisTrait;

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
	 * @Route("/status/")
	 */
	public function index() {
		$predis = $this->getPredis();

		$queue_len = $predis->ZCARD(MessageQueue::REDIS_MESSAGE_QUEUE);

		return $this->render('status.html.twig', [
			'jobs' => $this->_message_queue_gateway->getEventsByType(),
			'stats' => [
				'Queue Len' => $queue_len
			],
		]);
	}

}
