<?php

namespace Homie\Switches\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Application\UserException;
use BrainExe\Core\Translation\TranslationTrait;
use Homie\Switches\Switches;
use Homie\Switches\VO\ArduinoSwitchVO;
use Homie\Switches\VO\GpioSwitchVO;
use Homie\Switches\VO\ParticleVO;
use Homie\Switches\VO\RadioVO;
use Homie\Switches\VO\SwitchVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation("Switches.Controller.Controller", requirements={"switchId":"\d+"})
 */
class Controller
{
    use TranslationTrait;

    /**
     * @var Switches
     */
    private $switches;

    /**
     * @Inject({
     *     "@Switches.Switches",
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
    public function index() : array
    {
        $switches = $this->switches->getAll();

        return [
            'switches'  => iterator_to_array($switches),
            'radioPins' => Switches::RADIO_PINS,
        ];
    }

    /**
     * @param Request $request
     * @return SwitchVO
     * @Route("/switches/", methods="POST", name="switch.add")
     */
    public function add(Request $request) : SwitchVO
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
     * @param int $switchId
     * @return bool
     * @Route("/switches/{switchId}/", name="switches.delete", methods="DELETE")
     */
    public function delete(Request $request, int $switchId)
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
    private function createSwitchVO(Request $request) : SwitchVO
    {
        switch ($request->request->get('type')) {
            case RadioVO::TYPE:
                $pin = $request->request->getAlnum('pin');
                $switchVo = new RadioVO();
                $switchVo->code = $request->request->getAlnum('code');
                $switchVo->pin  = $this->switches->getRadioPin($pin);
                return $switchVo;
            case GpioSwitchVO::TYPE:
                $switchVo = new GpioSwitchVO();
                $switchVo->pin = $request->request->getAlnum('pin');
                return $switchVo;
            case ArduinoSwitchVO::TYPE:
                $switchVo = new ArduinoSwitchVO();
                $switchVo->pin    = $request->request->getAlnum('pin');
                $switchVo->nodeId = $request->request->getAlnum('nodeId');
                return $switchVo;
            case ParticleVO::TYPE:
                $switchVo = new ParticleVO();
                $switchVo->function = $request->request->getAlnum('function');
                $switchVo->nodeId   = $request->request->getAlnum('nodeId');
                return $switchVo;
            default:
                throw new UserException(
                    $this->translate('Invalid switch type: %s', $request->request->get('type'))
                );
        }
    }
}
