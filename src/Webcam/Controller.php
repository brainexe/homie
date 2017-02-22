<?php

namespace Homie\Webcam;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\IdGeneratorTrait;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ControllerAnnotation
 */
class Controller
{
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
     * @param Webcam $webcam
     * @param Filesystem $filesystem
     */
    public function __construct(Webcam $webcam, Filesystem $filesystem)
    {
        $this->webcam     = $webcam;
        $this->filesystem = $filesystem;
    }

    /**
     * @return array
     * @Route("/webcam/", name="webcam.index", methods="GET")
     */
    public function index()
    {
        $files = $this->webcam->getFiles();

        return [
            'files' => $files
        ];
    }

    /**
     * @return WebcamVO
     * @Route("/webcam/recent/", name="webcam.recent", methods="GET")
     */
    public function loadRecent()
    {
        return $this->webcam->getRecentImage();
    }

    /**
     * @param Request $request
     * @param string $type
     * @return bool
     * @Route("/webcam/{type}/", name="webcam.take", methods="POST")
     */
    public function take(Request $request, string $type) : bool
    {
        $name = $this->generateUniqueId('webcam');

        $duration = (int)$request->request->get('duration');

        switch ($type) {
            case 'video':
                $event = new WebcamEvent($name, WebcamEvent::TAKE_VIDEO, $duration);
                break;
            case 'sound':
                $event = new WebcamEvent($name, WebcamEvent::TAKE_SOUND, $duration);
                break;
            default:
                $event = new WebcamEvent($name, WebcamEvent::TAKE_PHOTO);
        }
        $this->dispatchInBackground($event);

        return true;
    }

    /**
     * @Route("/webcam/file/{file}", name="webcam.delete", methods="DELETE", requirements={"file":".+"})
     * @param Request $request
     * @param string $file
     * @return bool
     */
    public function delete(Request $request, string $file) : bool
    {
        unset($request);

        return $this->webcam->delete($file);
    }

    /**
     * @Route("/webcam/file/{file}", name="webcam.getFile", requirements={"file":".+"})
     * @param Request $request
     * @param string $file
     * @return Response
     */
    public function getFile(Request $request, string $file) : Response
    {
        unset($request);
        $stream = $this->filesystem->readStream($file);
        $mime   = $this->filesystem->getMimetype($file);

        $response = new Response();
        $response->setContent(stream_get_contents($stream));
        $response->setSharedMaxAge(86400);
        $response->headers->set('Content-Type', $mime);
        $response->headers->set('Cache-Control', 'max-age=86400');

        return $response;
    }
}
