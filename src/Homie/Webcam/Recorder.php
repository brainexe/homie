<?php

namespace Homie\Webcam;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Client\ClientInterface;
use League\Flysystem\Filesystem;
use BrainExe\Annotations\Annotations\Inject;

/**
 * @Service("Webcam.Recorder")
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

    /**
     * @var string
     */
    private $videoCommand;

    /**
     * @var string
     */
    private $soundCommand;

    /**
     * @var string
     */
    private $photoCommand;

    /**
     * @Inject({
     *  "@HomieClient",
     *  "@RemoteFilesystem",
     *  "%webcam.command.photo%",
     *  "%webcam.command.video%",
     *  "%webcam.command.sound%"
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
        string $photoCommand,
        string $videoCommand,
        string $soundCommand
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
    public function takePhoto(string $name)
    {
        $filename = sprintf('%s.jpg', $name);
        $this->take($this->photoCommand, $filename, WebcamEvent::TOOK_PHOTO);
    }

    /**
     * @param string $name
     * @param int $duration
     */
    public function takeVideo(string $name, int $duration)
    {
        $filename = sprintf('%s.avi', $name);
        $command = str_replace('{{duration}}', $duration, $this->videoCommand);
        $this->take($command, $filename, WebcamEvent::TOOK_VIDEO);
    }

    /**
     * @param string $name
     * @param int $duration
     */
    public function takeSound(string $name, int $duration)
    {
        $filename = sprintf('%s.wav', $name);
        $command  = str_replace('{{duration}}', $duration, $this->soundCommand);
        $this->take($command, $filename, WebcamEvent::TOOK_SOUND);
    }

    /**
     * @param string $command
     * @param string $filename
     * @param string $eventName
     */
    private function take(string $command, string $filename, string $eventName)
    {
        $temp = tempnam(sys_get_temp_dir(), 'webcam');

        $this->client->executeWithReturn(str_replace('{{file}}', $temp, $command));

        $this->filesystem->writeStream(Webcam::ROOT . $filename, fopen($temp, 'r'), [
            'visibility' => 'public'
        ]);

        unlink($temp);

        // todo push WebcamVO
        $event = new WebcamEvent($filename, $eventName);
        $this->dispatchEvent($event);
    }
}
