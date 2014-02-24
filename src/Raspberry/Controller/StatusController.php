<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Matze\Core\MessageQueue\MessageQueue;
use Matze\Core\Traits\RedisTrait;
use Matze\Core\Traits\TwigTrait;
use Raspberry\Dashboard\Dashboard;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class StatusController extends AbstractController {

	use RedisTrait;

	/**
	 * @return string
	 */
	public function getRoutes() {
		return [
			'status.index' => [
				'pattern' => '/status/',
				'defaults' => ['_controller' => 'Status::index']
			]
		];
	}

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
