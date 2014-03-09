<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\MessageQueue\MessageQueue;
use Matze\Core\Traits\RedisTrait;
use Matze\Core\Traits\TwigTrait;

/**
 * @Controller
 */
class StatusController extends AbstractController {

	use RedisTrait;

	/**
	 * @Route("/status/")
	 */
	public function index() {
		$predis = $this->getPredis();

		$queue_len = $predis->LLEN(MessageQueue::REDIS_MESSAGE_QUEUE);

		return $this->render('status.html.twig', [
			'stats' => [
				'Queue Len' => $queue_len
			],
		]);
	}

}
