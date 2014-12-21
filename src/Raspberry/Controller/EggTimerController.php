<?php

namespace Raspberry\Controller;

use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\MessageQueue\MessageQueueJob;
use Raspberry\EggTimer\EggTimer;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class EggTimerController implements ControllerInterface
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
     * @Route("/egg_timer/", name="egg_timer.index")
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
     * @return MessageQueueJob[]
     * @Route("/egg_timer/add/", name="egg_timer.add", methods="POST")
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
     * @return MessageQueueJob[]
     * @Route("/egg_timer/delete/{job_id}/", name="egg_timer.delete", methods="POST")
     */
    public function deleteEggTimer(Request $request, $jobId)
    {
        $this->timer->deleteJob($jobId);

        $currentJobs = $this->timer->getJobs();

        return $currentJobs;
    }
}
