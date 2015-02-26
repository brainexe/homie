<?php

namespace Raspberry\Radio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\Traits\AddFlashTrait;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Raspberry\Radio\RadioChangeEvent;
use Raspberry\Radio\RadioJob;
use Raspberry\Radio\Radios;
use Raspberry\Radio\VO\RadioVO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation
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
     * @Route("/radio/", name="radio.index")
     */
    public function index()
    {
        $radiosFormatted = $this->radios->getRadios();
        $jobs            = $this->radioJob->getJobs();

        return [
            'radios'     => $radiosFormatted,
            'radio_jobs' => $jobs,
            'pins'       => Radios::$radioPins,
        ];
    }

    /**
     * @param Request $request
     * @param integer $radioId
     * @param integer $status
     * @return JsonResponse
     * @Route("/radio/status/{radioId}/{status}/", name="radio.set_status", methods="POST")
     */
    public function setStatus(Request $request, $radioId, $status)
    {
        unset($request);

        $radioVo = $this->radios->getRadio($radioId);

        $event = new RadioChangeEvent($radioVo, $status);
        $this->dispatchInBackground($event);

        $response = new JsonResponse(true);
        $this->addFlash($response, self::ALERT_SUCCESS, _('Set Radio'));

        return $response;
    }

    /**
     * @param Request $request
     * @return RadioVO
     * @Route("/radio/add/", methods="POST")
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
     * @Route("/radio/delete/{radioId}/", name="radio.delete", methods="POST")
     */
    public function deleteRadio(Request $request, $radioId)
    {
        unset($request);

        $this->radios->deleteRadio($radioId);

        return true;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/radio/job/add/", name="radiojob.add", methods="POST")
     */
    public function addRadioJob(Request $request)
    {
        $radioId     = $request->request->getInt('radioId');
        $status      = $request->request->getInt('status');
        $timeString  = $request->request->get('time');

        $radioVo = $this->radios->getRadio($radioId);

        $this->radioJob->addRadioJob($radioVo, $timeString, $status);

        $response = new JsonResponse(true);
        $this->addFlash($response, self::ALERT_SUCCESS, _('The job was sored successfully'));

        return $response;
    }

    /**
     * @param Request $request
     * @param string $jobId
     * @return boolean
     * @Route("/radio/job/delete/{job_id}/", methods="POST")
     */
    public function deleteRadioJob(Request $request, $jobId)
    {
        unset($request);

        $this->radioJob->deleteJob($jobId);

        return true;
    }
}
