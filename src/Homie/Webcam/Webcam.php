<?php

namespace Homie\Webcam;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use League\Flysystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Service(public=false)
 */
class Webcam
{

    const ROOT       = 'Webcam/';
    const EXTENSION  = 'jpg';
    const TIMEOUT    = 10000;
    const EXECUTABLE = 'fswebcam';

    use EventDispatcherTrait;

    /**
     * @var ProcessBuilder
     */
    private $processBuilder;

    /**
     * @var Filesystem
     */
    private $remoteFilesystem;

    /**
     * @Inject({"@ProcessBuilder", "@RemoteFilesystem"})
     * @param ProcessBuilder $processBuilder
     * @param Filesystem $fileUploader
     */
    public function __construct(
        ProcessBuilder $processBuilder,
        Filesystem $fileUploader
    ) {
        $this->processBuilder   = $processBuilder;
        $this->remoteFilesystem = $fileUploader;
    }

    /**
     * @return WebcamVO[]
     */
    public function getPhotos()
    {
        $files = $this->remoteFilesystem->listContents(self::ROOT, true);

        $vos = [];
        foreach ($files as $file) {
            $webcamVo = $vos[]   = new WebcamVO();
            $webcamVo->filePath  = $file['path'];
            $webcamVo->name      = $file['basename'];
            $webcamVo->webcamId  = $file['basename'];
            $webcamVo->webPath   = sprintf('%s%s', self::ROOT, $webcamVo->name);
            $webcamVo->timestamp = isset($file['timestamp']) ? $file['timestamp'] : null;
        }

        return $vos;
    }

    /**
     * @param string $name
     */
    public function takePhoto($name)
    {
        $path = $this->getFilename($name);

        $temp = tempnam(sys_get_temp_dir(), 'webcam');
        $process = $this->processBuilder
            ->setArguments([self::EXECUTABLE, '-d', '/dev/video0', $temp])
            ->setTimeout(self::TIMEOUT)
            ->getProcess();

        $process->run();

        $event = new WebcamEvent($name, WebcamEvent::TOOK_PHOTO);
        $this->dispatchEvent($event);

        $this->remoteFilesystem->writeStream(self::ROOT . basename($path), fopen($temp, 'r'));

        unlink($temp);
    }

    /**
     * @param string $filename
     */
    public function delete($filename)
    {
        $this->remoteFilesystem->delete($filename);
    }

    /**
     * @param string $shotId
     * @return string
     */
    public function getFilename($shotId)
    {
        return sprintf('%s%s%s.%s', ROOT, self::ROOT, $shotId, self::EXTENSION);
    }
}
