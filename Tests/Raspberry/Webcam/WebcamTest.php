<?php

namespace Tests\Raspberry\Webcam\Webcam;

use ArrayIterator;
use BrainExe\Core\Util\FileUploader;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Webcam\Webcam;
use Raspberry\Webcam\WebcamEvent;
use Raspberry\Webcam\WebcamVO;
use Symfony\Component\Filesystem\Filesystem;
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
     * @var Finder|MockObject
     */
    private $mockFinder;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    /**
     * @var FileUploader|MockObject
     */
    private $mockFileUploader;

    public function setUp()
    {
        $this->mockFilesystem      = $this->getMock(Filesystem::class, [], [], '', false);
        $this->mockProcessBuilder  = $this->getMock(ProcessBuilder::class, [], [], '', false);
        $this->mockFinder          = $this->getMock(Finder::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->mockFileUploader     = $this->getMock(FileUploader::class, [], [], '', false);

        $this->subject = new Webcam(
            $this->mockFilesystem,
            $this->mockProcessBuilder,
            $this->mockFinder,
            $this->mockFileUploader
        );
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testGetPhotos()
    {
        $directory = ROOT . Webcam::ROOT;

        $filePath         = 'file path';
        $relativeFilePath = 'relative path name';
        $fileCTime        = 10;
        $fileBaseName     = 'relative.ext';

        $file = $this->getMock(SplFileInfo::class, [], [], '', false);

        $file->expects($this->once())
            ->method('getPath')
            ->willReturn($filePath);

        $file->expects($this->once())
            ->method('getRelativePathname')
            ->willReturn($relativeFilePath);

        $file->expects($this->once())
            ->method('getCTime')
            ->willReturn($fileCTime);

        $file->expects($this->once())
            ->method('getBasename')
            ->willReturn($fileBaseName);

        $expectedWebcam = new WebcamVO();
        $expectedWebcam->filePath = $filePath;
        $expectedWebcam->webcamId = $fileBaseName;
        $expectedWebcam->name = $relativeFilePath;
        $expectedWebcam->webPath = 'static/webcam/relative path name';
        $expectedWebcam->timestamp = $fileCTime;

        $this->mockFilesystem
            ->expects($this->once())
            ->method('exists')
            ->with($directory)
            ->willReturn(false);

        $this->mockFilesystem
            ->expects($this->once())
            ->method('mkdir')
            ->with($directory, 0777);

        $this->mockFinder
            ->expects($this->at(0))
            ->method('files')
            ->willReturn($this->mockFinder);

        $this->mockFinder
            ->expects($this->at(1))
            ->method('in')
            ->with($directory)
            ->willReturn($this->mockFinder);

        $this->mockFinder
            ->expects($this->at(2))
            ->method('name')
            ->with('*.jpg')
            ->willReturn($this->mockFinder);

        $this->mockFinder
            ->expects($this->at(3))
            ->method('sortByName')
            ->willReturn($this->mockFinder);

        $this->mockFinder
            ->expects($this->at(4))
            ->method('getIterator')
            ->willReturn(new ArrayIterator([$file]));

        $actualResult = $this->subject->getPhotos();

        $this->assertEquals([$expectedWebcam], $actualResult);
        $this->assertEquals($relativeFilePath, $expectedWebcam->getWebcamId());
    }

    public function testTakePhoto()
    {
        $name = 'name';
        $path = ROOT . Webcam::ROOT . $name . '.' . Webcam::EXTENSION;

        $process = $this->getMock(Process::class, [], [], '', false);

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->with([Webcam::EXECUTABLE, '-d', '/dev/video0', $path])
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

        $this->mockFileUploader
            ->expects($this->once())
            ->method('upload');

        $this->subject->takePhoto($name);
    }

    public function testDelete()
    {
        $webcamId = 'id';

        $this->mockFilesystem
            ->expects($this->once())
            ->method('remove')
            ->with(ROOT . Webcam::ROOT . 'id.jpg');

        $this->subject->delete($webcamId);
    }

    public function testGetFilename()
    {
        $webcamId = '5';

        $actualResult = $this->subject->getFilename($webcamId);
        $this->assertEquals(ROOT . Webcam::ROOT . $webcamId  . '.' . Webcam::EXTENSION, $actualResult);
    }
}
