<?php

namespace Homie\Radio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\Traits\AddFlashTrait;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\MessageQueue\Job;
use Homie\Radio\VO\RadioVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("RadioController")
 */
class Controller implements ControllerInterface
{

    use AddFlashTrait;
    use EventDispatcherTrait;

    /**
     * @var Radios;
     */
    private $radios;

    /**
     * @var RadioJob
     */
    private $radioJob;

    /**
     * @Inject({"@Radios", "@RadioJob"})
     * @param Radios $radios
     * @param RadioJob $job
     */
    public function __construct(Radios $radios, RadioJob $job)
    {
        $this->radios   = $radios;
        $this->radioJob = $job;
    }

    /**
     * @return array
     * @Route("/radios/", name="radio.index", methods="GET")
     */
    public function index()
    {
        $radiosFormatted = $this->radios->getRadios();
        $jobs            = $this->radioJob->getJobs();

        return [
            'radios'    => $radiosFormatted,
            'radioJobs' => $jobs,
            'pins'      => Radios::$radioPins,
        ];
    }

    /**
     * @param Request $request
     * @param integer $radioId
     * @param integer $status
     * @return bool
     * @Route("/radios/{radioId}/status/{status}/", name="radio.set_status", methods="POST")
     */
    public function setStatus(Request $request, $radioId, $status)
    {
        unset($request);

        $radioVo = $this->radios->getRadio($radioId);

        $event = new RadioChangeEvent($radioVo, (bool)$status);
        $this->dispatchInBackground($event);

        return true;
    }

    /**
     * @param Request $request
     * @return RadioVO
     * @Route("/radios/", methods="POST")
     */
    public function addRadio(Request $request)
    {
        $name        = $request->request->get('name');
        $description = $request->request->get('description');
        $code        = $request->request->get('code');
        $pinRaw      = $request->request->get('pin');

        $pin = $this->radios->getRadioPin($pinRaw);

        $radio = new RadioVO();
        $radio->name        = $name;
        $radio->description = $description;
        $radio->code        = $code;
        $radio->pin         = $pin;

        $this->radios->addRadio($radio);

        return $radio;
    }

    /**
     * @param Request $request
     * @param integer $radioId
     * @return boolean
     * @Route("/radios/{radioId}/", name="radio.delete", methods="DELETE")
     */
    public function deleteRadio(Request $request, $radioId)
    {
        unset($request);

        $this->radios->deleteRadio($radioId);

        return true;
    }

    /**
     * @param Request $request
     * @return Job[]
     * @Route("/radios/jobs/", name="radiojob.add", methods="POST")
     */
    public function addRadioJob(Request $request)
    {
        $radioId     = $request->request->getAlnum('radioId');
        $status      = (bool)$request->request->getInt('status');
        $timeString  = $request->request->get('time');

        $radioVo = $this->radios->getRadio($radioId);

        $this->radioJob->addRadioJob($radioVo, $timeString, $status);

        return $this->radioJob->getJobs();
    }

    /**
     * @param Request $request
     * @param string $jobId
     * @return boolean
     * @Route("/radios/jobs/{job_id}/", methods="DELETE")
     */
    public function deleteRadioJob(Request $request, $jobId)
    {
        unset($request);

        $this->radioJob->deleteJob($jobId);

        return true;
    }
}
