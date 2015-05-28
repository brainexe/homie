<?php

namespace Homie\EggTimer;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\MessageQueue\Job;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("EggTimerController")
 */
class Controller
{

    /**
     * @var EggTimer
     */
    private $timer;

    /**
     * @Inject({"@EggTimer"})
     * @param EggTimer $timer
     */
    public function __construct(EggTimer $timer)
    {
        $this->timer = $timer;
    }

    /**
     * @return array
     * @Route("/egg_timer/", name="egg_timer.index", methods="GET")
     */
    public function index()
    {
        $currentJobs = $this->timer->getJobs();

        return [
            'jobs' => $currentJobs
        ];
    }

    /**
     * @param Request $request
     * @return Job[]
     * @Route("/egg_timer/", name="egg_timer.add", methods="POST")
     */
    public function add(Request $request)
    {
        $text = $request->request->get('text');
        $time = $request->request->get('time');

        $this->timer->addNewJob($time, $text);

        $currentJobs = $this->timer->getJobs();

        return $currentJobs;
    }

    /**
     * @param Request $request
     * @param string $jobId
     * @return Job[]
     * @Route("/egg_timer/{job_id}/", name="egg_timer.delete", methods="DELETE")
     */
    public function deleteEggTimer(Request $request, $jobId)
    {
        unset($request);

        $this->timer->deleteJob($jobId);

        $currentJobs = $this->timer->getJobs();

        return $currentJobs;
    }
}
