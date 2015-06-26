<?php

namespace Homie\Webcam;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Client\ClientInterface;
use League\Flysystem\Filesystem;

/**
 * @Service(public=false)
 */
class Webcam
{
    const ROOT       = 'Webcam/';
    const EXTENSION  = 'jpg';

    use EventDispatcherTrait;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Filesystem
     */
    private $remoteFilesystem;

    /**
     * @Inject({"@HomieClient", "@Filesystem", "%webcam.executable%"})
     * @param ClientInterface $client
     * @param Filesystem $fileUploader
     * @param string $command
     */
    public function __construct(
        ClientInterface $client,
        Filesystem $fileUploader,
        $command
    ) {
        $this->client   = $client;
        $this->remoteFilesystem = $fileUploader;
        $this->command = $command;
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

        $command = sprintf($this->command, $temp);
        $this->client->execute($command);

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
