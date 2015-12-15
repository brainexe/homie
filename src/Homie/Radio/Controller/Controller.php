<?php

namespace Homie\Radio\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Radio\Job;
use Homie\Radio\Radios;
use Homie\Radio\SwitchChangeEvent;
use Homie\Radio\VO\RadioVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Switch.Controller.Controller")
 */
class Controller
{
    use EventDispatcherTrait;

    /**
     * @var Radios;
     */
    private $radios;

    /**
     * @var Job
     */
    private $job;

    /**
     * @Inject({
     *     "@Radios",
     *     "@Switch.Job"
     * })
     * @param Radios $radios
     * @param Job $job
     */
    public function __construct(
        Radios $radios,
        Job $job
    ) {
        $this->radios = $radios;
        $this->job    = $job;
    }

    /**
     * @return array
     * @Route("/radios/", name="radio.index", methods="GET")
     */
    public function index()
    {
        $radiosFormatted = $this->radios->getRadios();

        return [
            'radios' => iterator_to_array($radiosFormatted),
            'pins'   => Radios::PINS,
        ];
    }

    /**
     * @param Request $request
     * @param integer $switchId
     * @param integer $status
     * @return bool
     * @Route("/radios/{switchId}/status/{status}/", name="radio.set_status", methods="POST")
     */
    public function setStatus(Request $request, $switchId, $status)
    {
        unset($request);

        $radioVo = $this->radios->get($switchId);

        $event = new SwitchChangeEvent($radioVo, (bool)$status);
        $this->dispatchInBackground($event);

        return true;
    }

    /**
     * @param Request $request
     * @return RadioVO
     * @Route("/radios/", methods="POST", name="radio.add")
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
     * @param integer $switchId
     * @return boolean
     * @Route("/radios/{switchId}/", name="radio.delete", methods="DELETE")
     */
    public function deleteRadio(Request $request, $switchId)
    {
        unset($request);

        $this->radios->delete($switchId);

        return true;
    }

    /**
     * @param Request $request
     * @return true
     * @Route("/radios/jobs/", name="radiojob.add", methods="POST")
     */
    public function addJob(Request $request)
    {
        $switchId    = $request->request->getInt('radioId');
        $status      = (bool)$request->request->getInt('status');
        $timeString  = (string)$request->request->get('time');

        $switch = $this->radios->get($switchId);

        $this->job->addJob($switch, $timeString, $status);

        return true;
    }
}
