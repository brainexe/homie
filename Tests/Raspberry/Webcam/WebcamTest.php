<?php

namespace Tests\Raspberry\Webcam\Webcam;

use ArrayIterator;
use BrainExe\Core\Util\FileUploader;
use League\Flysystem\Filesystem;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Webcam\Webcam;
use Raspberry\Webcam\WebcamEvent;
use Raspberry\Webcam\WebcamVO;
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
    private $mockFilesystem;

    /**
     * @var ProcessBuilder|MockObject
     */
    private $mockProcessBuilder;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    public function setUp()
    {
        $this->mockFilesystem      = $this->getMock(Filesystem::class, [], [], '', false);
        $this->mockProcessBuilder  = $this->getMock(ProcessBuilder::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new Webcam(
            $this->mockProcessBuilder,
            $this->mockFilesystem
        );
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testGetPhotos()
    {
        $directory = ROOT . Webcam::ROOT;

        $filePath         = '/www/something/relative.ext';
        $fileBaseName     = 'relative.ext';
        $fileCTime        = 10;

        $files = [
            [
                'path' => $filePath,
                'basename' => $fileBaseName,
                'timestamp' => $fileCTime
            ]
        ];

        $this->mockFilesystem
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
        $path = ROOT . Webcam::ROOT . $name . '.' . Webcam::EXTENSION;

        $process = $this->getMock(Process::class, [], [], '', false);

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setArguments')
//            ->with([Webcam::EXECUTABLE, '-d', '/dev/video0', ])
            ->willReturn($this->mockProcessBuilder);

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setTimeout')
            ->with(Webcam::TIMEOUT)
            ->willReturn($this->mockProcessBuilder);

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $process->expects($this->once())
            ->method('run');

        $event = new WebcamEvent($name, WebcamEvent::TOOK_PHOTO);

        $this->mockEventDispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->mockFilesystem
            ->expects($this->once())
            ->method('writeStream')
            ->with(Webcam::ROOT . $name . '.jpg');

        $this->subject->takePhoto($name);
    }

    public function testDelete()
    {
        $filename = 'id';

        $this->mockFilesystem
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
