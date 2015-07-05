<?php

namespace Tests\Homie\Webcam\Webcam;

use ArrayIterator;
use BrainExe\Core\Util\FileUploader;
use Homie\Client\ClientInterface;
use Homie\Webcam\Recorder;
use League\Flysystem\Filesystem;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Webcam\Webcam;
use Homie\Webcam\WebcamEvent;
use Homie\Webcam\WebcamVO;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Finder\Finder;
use BrainExe\Core\EventDispatcher\EventDispatcher;

class RecorderTest extends PHPUnit_Framework_TestCase
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
        $this->filesystem = $this->getMock(Filesystem::class, [], [], '', false);
        $this->client     = $this->getMock(ClientInterface::class, [], [], '', false);
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new Recorder(
            $this->client,
            $this->filesystem,
            'photo command %s',
            'video command %s',
            'sound command %s'
        );
        $this->subject->setEventDispatcher($this->dispatcher);
    }


    public function testTakePhoto()
    {
        $name = 'name';

        $this->client
            ->expects($this->once())
            ->method('execute')
            ->with($this->stringStartsWith("photo command /tmp/web"));

        $event = new WebcamEvent($name, WebcamEvent::TOOK_PHOTO);

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
}
