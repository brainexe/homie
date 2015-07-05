<?php

namespace Homie\Webcam;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Client\ClientInterface;
use League\Flysystem\Filesystem;
use BrainExe\Annotations\Annotations\Inject;

/**
 * @Service("Webcam.Recorder", public=false)
 */
class Recorder
{

    use EventDispatcherTrait;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Filesystem
     */
    private $filesystem;
    private $videoCommand;
    private $soundCommand;

    /**
     * @Inject({
     * "@HomieClient",
     * "@RemoteFilesystem",
     * "%webcam.executable.photo%",
     * "%webcam.executable.video%",
     * "%webcam.executable.sound%"
     * })
     * @param ClientInterface $client
     * @param Filesystem $fileUploader
     * @param string $photoCommand
     * @param string $videoCommand
     * @param string $soundCommand
     */
    public function __construct(
        ClientInterface $client,
        Filesystem $fileUploader,
        $photoCommand,
        $videoCommand,
        $soundCommand
    ) {
        $this->client       = $client;
        $this->filesystem   = $fileUploader;
        $this->photoCommand = $photoCommand;
        $this->videoCommand = $videoCommand;
        $this->soundCommand = $soundCommand;
    }

    /**
     * @param string $name
     */
    public function takePhoto($name)
    {
        $filename = sprintf('%s.jpg', $name);

        $this->take($this->photoCommand, $filename);

        $event = new WebcamEvent($name, WebcamEvent::TOOK_PHOTO);
        $this->dispatchEvent($event);
    }

    /**
     * @param string $command
     * @param string $filename
     */
    private function take($command, $filename)
    {
        $temp = tempnam(sys_get_temp_dir(), 'webcam');

        $this->client->execute(sprintf($command, $temp));

        $this->filesystem->writeStream(Webcam::ROOT . $filename, fopen($temp, 'r'));

        unlink($temp);
    }

    /**
     * @param string $name
     * @param int $duration
     */
    public function takeVideo($name, $duration)
    {
        // todo implement
    }

    /**
     * @param string $name
     * @param int $duration
     */
    public function takeSound($name, $duration)
    {
        // todo implement
    }
}
