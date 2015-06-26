<?php

namespace Tests\Homie\Webcam\Webcam;

use ArrayIterator;
use BrainExe\Core\Util\FileUploader;
use Homie\Client\ClientInterface;
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

class WebcamTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Webcam
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

        $this->subject = new Webcam(
            $this->client,
            $this->filesystem,
            'command %s'
        );
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testGetPhotos()
    {
        $filePath     = '/www/something/relative.ext';
        $fileBaseName = 'relative.ext';
        $fileCTime    = 10;

        $files = [
            [
                'path' => $filePath,
                'basename' => $fileBaseName,
                'timestamp' => $fileCTime
            ]
        ];

        $this->filesystem
            ->expects($this->once())
            ->method('listContents')
            ->with(Webcam::ROOT)
            ->willReturn($files);

        $expectedWebcam            = new WebcamVO();
        $expectedWebcam->filePath  = $filePath;
        $expectedWebcam->webcamId  = $fileBaseName;
        $expectedWebcam->name      = $fileBaseName;
        $expectedWebcam->webPath   = 'Webcam/relative.ext';
        $expectedWebcam->timestamp = $fileCTime;

        $actualResult = $this->subject->getPhotos();

        $this->assertEquals([$expectedWebcam], $actualResult);
        $this->assertEquals($fileBaseName, $expectedWebcam->getWebcamId());
    }

    public function testTakePhoto()
    {
        $name = 'name';

        $this->client
            ->expects($this->once())
            ->method('execute')
            ->with($this->stringStartsWith("command /tmp/web"));

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

    public function testDelete()
    {
        $filename = 'id';

        $this->filesystem
            ->expects($this->once())
            ->method('delete')
            ->with($filename);

        $this->subject->delete($filename);
    }

    public function testGetFilename()
    {
        $webcamId = '5';

        $actualResult = $this->subject->getFilename($webcamId);
        $this->assertEquals(ROOT . Webcam::ROOT . $webcamId  . '.' . Webcam::EXTENSION, $actualResult);
    }
}
