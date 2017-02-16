<?php

namespace Tests\Homie\Webcam\Webcam;

use Homie\Client\ClientInterface;
use Homie\Webcam\Recorder;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Webcam\Webcam;
use Homie\Webcam\WebcamEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;

class RecorderTest extends TestCase
{

    /**
     * @var Recorder
     */
    private $subject;

    /**
     * @var Filesystem|MockObject
     */
    private $filesystem;

    /**
     * @var ClientInterface|MockObject
     */
    private $client;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->client     = $this->createMock(ClientInterface::class);
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new Recorder(
            $this->client,
            $this->filesystem,
            'photo command {{file}}',
            'video command {{file}} {{duration}}',
            'sound command {{file}} {{duration}}'
        );
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testTakePhoto()
    {
        $name = 'name';

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with($this->stringStartsWith("photo command /tmp/web"));

        $event = new WebcamEvent($name.'.jpg', WebcamEvent::TOOK_PHOTO);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->filesystem
            ->expects($this->once())
            ->method('writeStream')
            ->with(Webcam::ROOT . $name . '.jpg');

        $this->subject->takePhoto($name);
    }

    public function testTakeVideo()
    {
        $name = 'name';

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with($this->stringStartsWith("video command /tmp/web"));

        $event = new WebcamEvent($name.'.avi', WebcamEvent::TOOK_VIDEO);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->filesystem
            ->expects($this->once())
            ->method('writeStream')
            ->with(Webcam::ROOT . $name . '.avi');

        $this->subject->takeVideo($name, 5);
    }

    public function testTakeSound()
    {
        $name = 'name';

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with($this->stringStartsWith("sound command /tmp/web"));

        $event = new WebcamEvent($name.'.wav', WebcamEvent::TOOK_SOUND);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->filesystem
            ->expects($this->once())
            ->method('writeStream')
            ->with(Webcam::ROOT . $name . '.wav');

        $this->subject->takeSound($name, 5);
    }
}
