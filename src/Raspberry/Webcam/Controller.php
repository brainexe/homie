<?php

namespace Raspberry\Webcam;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\Traits\AddFlashTrait;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\IdGeneratorTrait;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ControllerAnnotation("WebcamController")
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
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @Inject({"@Webcam", "@RemoteFilesystem"})
     * @param Webcam $webcam
     * @param Filesystem $filesystem
     */
    public function __construct(Webcam $webcam, Filesystem $filesystem)
    {
        $this->webcam = $webcam;
        $this->filesystem = $filesystem;
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
        $filename = $request->request->get('shotId');

        $this->webcam->delete($filename);

        return true;
    }

    /**
     * @Route("/webcam/image/", name="webcam.image")
     * @param Request $request
     * @return boolean
     */
    public function getImage(Request $request)
    {
        $file   = $request->query->get('file');
        $stream = $this->filesystem->readStream($file);
        $mime   = $this->filesystem->getMimetype($file);

        $response = new Response();
        $response->setContent(stream_get_contents($stream));
        $response->headers->set('Content-Type', $mime);
        $response->headers->set('Cache-Control', 'max-age=86400, must-revalidate');

        return $response;
    }

}
