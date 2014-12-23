<?php

namespace Tests\Raspberry\Webcam\Webcam;

use ArrayIterator;
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

    public function setUp()
    {
        $this->mockFilesystem = $this->getMock(Filesystem::class, [], [], '', false);
        $this->mockProcessBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);
        $this->mockFinder = $this->getMock(Finder::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new Webcam($this->mockFilesystem, $this->mockProcessBuilder, $this->mockFinder);
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testGetPhotos()
    {
        $directory = ROOT . Webcam::ROOT;

        $file_path = 'file path';
        $relative_file_path = 'relative path name';
        $file_c_time = 10;
        $file_base_name = 'relative.ext';

        $file = $this->getMock(SplFileInfo::class, [], [], '', false);

        $file->expects($this->once())
        ->method('getPath')
        ->willReturn($file_path);

        $file->expects($this->once())
        ->method('getRelativePathname')
        ->willReturn($relative_file_path);

        $file->expects($this->once())
        ->method('getCTime')
        ->willReturn($file_c_time);

        $file->expects($this->once())
        ->method('getBasename')
        ->willReturn($file_base_name);

        $expected_webcam_vo = new WebcamVO();
        $expected_webcam_vo->filePath = $file_path;
        $expected_webcam_vo->webcamId = $file_base_name;
        $expected_webcam_vo->name = $relative_file_path;
        $expected_webcam_vo->webPath = 'static/webcam/relative path name';
        $expected_webcam_vo->timestamp = $file_c_time;

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

        $this->assertEquals([$expected_webcam_vo], $actualResult);
        $this->assertEquals($relative_file_path, $expected_webcam_vo->getWebcamId());
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

        $this->subject->takePhoto($name);
    }

    public function testDelete()
    {
        $id = 'id';

        $this->mockFilesystem
        ->expects($this->once())
        ->method('remove')
        ->with(ROOT . Webcam::ROOT . 'id.jpg');

        $this->subject->delete($id);
    }

    public function testGetFilename()
    {
        $id = '5';

        $actualResult = $this->subject->getFilename($id);
        $this->assertEquals(ROOT . Webcam::ROOT . $id  . '.' . Webcam::EXTENSION, $actualResult);
    }
}
