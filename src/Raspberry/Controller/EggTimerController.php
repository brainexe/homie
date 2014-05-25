<?php

namespace Raspberry\Controller;

use Matze\Core\Controller\AbstractController;
use Raspberry\EggTimer\EggTimer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class EggTimerController extends AbstractController {

	/**
	 * @var EggTimer
	 */
	private $_egg_timer;

	/**
	 * @Inject({"@EggTimer"})
	 */
	public function __construct(EggTimer $egg_timer) {
		$this->_egg_timer = $egg_timer;
	}

	/**
	 * @return string
	 * @Route("/egg_timer/", name="egg_timer.index")
	 */
	public function index() {
		$current_jobs = $this->_egg_timer->getJobs();

		return $this->render('egg_timer.html.twig', [
			'jobs' => $current_jobs
		]);
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @Route("/egg_timer/add/", name="egg_timer.add", methods="POST")
	 */
	public function add(Request $request) {
		$text = $request->request->get('text');
		$time = $request->request->get('time');

		$this->_egg_timer->addNewJob($time, $text);

		return new RedirectResponse('/egg_timer/');
	}

	/**
	 * @param string $job_id
	 * @return RedirectResponse
	 * @Route("/egg_timer/delete/{job_id}/", name="egg_timer.delete", csrf=true)
	 */
	public function deleteEggTimer($job_id) {
		$this->_egg_timer->deleteJob($job_id);

		return new RedirectResponse('/egg_timer/');
	}
}