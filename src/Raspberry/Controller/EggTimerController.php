<?php

namespace Raspberry\Controller;


use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\MessageQueue\MessageQueueJob;
use Raspberry\EggTimer\EggTimer;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class EggTimerController implements ControllerInterface {

	/**
	 * @var EggTimer
	 */
	private $_egg_timer;

	/**
	 * @Inject({"@EggTimer"})
	 * @param EggTimer $egg_timer
	 */
	public function __construct(EggTimer $egg_timer) {
		$this->_egg_timer = $egg_timer;
	}

	/**
	 * @return array
	 * @Route("/egg_timer/", name="egg_timer.index")
	 */
	public function index() {
		$current_jobs = $this->_egg_timer->getJobs();

		return [
			'jobs' => $current_jobs
		];
	}

	/**
	 * @param Request $request
	 * @return MessageQueueJob[]
	 * @Route("/egg_timer/add/", name="egg_timer.add", methods="POST")
	 */
	public function add(Request $request) {
		$text = $request->request->get('text');
		$time = $request->request->get('time');

		$this->_egg_timer->addNewJob($time, $text);

		$current_jobs = $this->_egg_timer->getJobs();

		return $current_jobs;
	}

	/**
	 * @param Request $request
	 * @param string $job_id
	 * @return MessageQueueJob[]
	 * @Route("/egg_timer/delete/{job_id}/", name="egg_timer.delete", methods="POST")
	 */
	public function deleteEggTimer(Request $request, $job_id) {
		$this->_egg_timer->deleteJob($job_id);

		$current_jobs = $this->_egg_timer->getJobs();

		return $current_jobs;
	}
}