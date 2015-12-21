<?php

namespace Homie\Switches\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Application\UserException;
use Homie\Switches\Switches;

use Homie\Switches\VO\GpioSwitchVO;
use Homie\Switches\VO\RadioVO;
use Homie\Switches\VO\SwitchVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Switch.Controller.Controller")
 */
class Controller
{

    /**
     * @var Switches;
     */
    private $switches;

    /**
     * @Inject({
     *     "@Switch.Switches",
     * })
     * @param Switches $switches
     */
    public function __construct(
        Switches $switches
    ) {
        $this->switches = $switches;
    }

    /**
     * @return array
     * @Route("/switches/", name="switches.index", methods="GET")
     */
    public function index()
    {
        $switches = $this->switches->getAll();

        return [
            'switches'  => iterator_to_array($switches),
            'radioPins' => Switches::RADIO_PINS,
        ];
    }

    /**
     * @param Request $request
     * @return RadioVO
     * @Route("/switches/", methods="POST", name="switch.add")
     */
    public function add(Request $request)
    {
        $name        = $request->request->get('name');
        $description = $request->request->get('description');

        $switch = $this->createSwitchVO($request);
        $switch->name        = $name;
        $switch->description = $description;

        $this->switches->add($switch);

        return $switch;
    }

    /**
     * @param Request $request
     * @param integer $switchId
     * @return boolean
     * @Route("/switches/{switchId}/", name="switches.delete", methods="DELETE")
     */
    public function delete(Request $request, $switchId)
    {
        unset($request);

        $this->switches->delete($switchId);

        return true;
    }

    /**
     * @param Request $request
     * @return SwitchVO
     * @throws UserException
     */
    private function createSwitchVO(Request $request)
    {
        $type = $request->request->getAlnum('type');
        switch ($type) {
            case RadioVO::TYPE:
                $pin = $request->request->getAlnum('pin');
                $switchVo = new RadioVO();
                $switchVo->code = $request->request->getAlnum('code');
                $switchVo->pin  = $this->switches->getRadioPin($pin);
                break;
            case GpioSwitchVO::TYPE:
                $switchVo = new GpioSwitchVO();
                $switchVo->pin  = $request->request->getAlnum('pin');
                break;
            default:
                throw new UserException(sprintf(_('Invalid switch type: %s'), $type));
        }

        return $switchVo;
    }
}
