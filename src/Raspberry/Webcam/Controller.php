<?php

namespace Raspberry\Webcam;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\Traits\AddFlashTrait;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\IdGeneratorTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation
 */
class Controller implements ControllerInterface
{

    use AddFlashTrait;
    use EventDispatcherTrait;
    use IdGeneratorTrait;

    /**
     * @var Webcam
     */
    private $webcam;

    /**
     * @Inject("@Webcam")
     * @param Webcam $webcam
     */
    public function __construct(Webcam $webcam)
    {
        $this->webcam = $webcam;
    }

    /**
     * @return array
     * @Route("/webcam/", name="webcam.index")
     */
    public function index()
    {
        $shots = $this->webcam->getPhotos();

        return [
            'shots' => $shots
        ];
    }

    /**
     * @Route("/webcam/take/", name="webcam.take", csrf=true)
     */
    public function takePhoto()
    {
        $name = $this->generateRandomId();

        $event = new WebcamEvent($name, WebcamEvent::TAKE_PHOTO);
        $this->dispatchInBackground($event);

        $response = new JsonResponse(true);

        $this->addFlash($response, self::ALERT_INFO, 'Cheese...');

        return $response;
    }

    /**
     * @Route("/webcam/delete/", name="webcam.delete", csrf=true)
     * @param Request $request
     * @return boolean
     */
    public function delete(Request $request)
    {
        $shotId = $request->request->get('shot_id');

        $this->webcam->delete($shotId);

        return true;
    }
}
