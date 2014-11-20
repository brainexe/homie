<?php

namespace Tests\Raspberry\Webcam\Webcam;

use ArrayIterator;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Webcam\Webcam;
use Raspberry\Webcam\WebcamEvent;
use Raspberry\Webcam\WebcamVO;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Finder\Finder;
use BrainExe\Core\EventDispatcher\EventDispatcher;

class WebcamTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Webcam
	 */
	private $_subject;

	/**
	 * @var Filesystem|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockFilesystem;

	/**
	 * @var ProcessBuilder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockProcessBuilder;

	/**
	 * @var Finder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockFinder;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;

	public function setUp() {
		$this->_mockFilesystem = $this->getMock(Filesystem::class, [], [], '', false);
		$this->_mockProcessBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);
		$this->_mockFinder = $this->getMock(Finder::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new Webcam($this->_mockFilesystem, $this->_mockProcessBuilder, $this->_mockFinder);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testGetPhotos() {
		$directory = ROOT . Webcam::ROOT;

		$file_path = 'file path';
		$relative_file_path = 'relative path name';
		$file_c_time = 10;
		$file_base_name = 'relative.ext';

		$file = $this->getMock(SplFileInfo::class, [], [], '', false);

		$file->expects($this->once())
			->method('getPath')
			->will($this->returnValue($file_path));

		$file->expects($this->once())
			->method('getRelativePathname')
			->will($this->returnValue($relative_file_path));

		$file->expects($this->once())
			->method('getCTime')
			->will($this->returnValue($file_c_time));

		$file->expects($this->once())
			->method('getBasename')
			->will($this->returnValue($file_base_name));

		$expected_webcam_vo = new WebcamVO();
		$expected_webcam_vo->file_path = $file_path;
		$expected_webcam_vo->id = $file_base_name;
		$expected_webcam_vo->name = $relative_file_path;
		$expected_webcam_vo->web_path = 'static/webcam/relative path name';
		$expected_webcam_vo->timestamp = $file_c_time;

		$this->_mockFilesystem
			->expects($this->once())
			->method('exists')
			->with($directory)
			->will($this->returnValue(false));

		$this->_mockFilesystem
			->expects($this->once())
			->method('mkdir')
			->with($directory, 0777);

		$this->_mockFinder
			->expects($this->at(0))
			->method('files')
			->will($this->returnValue($this->_mockFinder));

		$this->_mockFinder
			->expects($this->at(1))
			->method('in')
			->with($directory)
			->will($this->returnValue($this->_mockFinder));

		$this->_mockFinder
			->expects($this->at(2))
			->method('name')
			->with('*.jpg')
			->will($this->returnValue($this->_mockFinder));

		$this->_mockFinder
			->expects($this->at(3))
			->method('sortByName')
			->will($this->returnValue($this->_mockFinder));

		$this->_mockFinder
			->expects($this->at(4))
			->method('getIterator')
			->will($this->returnValue(new ArrayIterator([$file])));

		$actual_result = $this->_subject->getPhotos();

		$this->assertEquals([$expected_webcam_vo], $actual_result);
		$this->assertEquals($relative_file_path, $expected_webcam_vo->getId());
	}

	public function testTakePhoto() {
		$name = 'name';
		$path = ROOT . Webcam::ROOT . $name . '.' . Webcam::EXTENSION;

		$process = $this->getMock(Process::class, [], [], '', false);

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('setArguments')
			->with([Webcam::EXECUTABLE, '-d', '/dev/video0', $path])
			->will($this->returnValue($this->_mockProcessBuilder));

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('setTimeout')
			->with(Webcam::TIMEOUT)
			->will($this->returnValue($this->_mockProcessBuilder));

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('getProcess')
			->will($this->returnValue($process));


		$process->expects($this->once())
			->method('run');

		$event = new WebcamEvent($name, WebcamEvent::TOOK_PHOTO);

		$this->_mockEventDispatcher
			->expects($this->once())
			->method('dispatchEvent')
			->with($event);

		$this->_subject->takePhoto($name);
	}

	public function testDelete() {
		$id = 'id';

		$this->_mockFilesystem
			->expects($this->once())
			->method('remove')
			->with(ROOT . Webcam::ROOT . 'id.jpg');

		$this->_subject->delete($id);
	}

	public function testGetFilename() {
		$id = '5';

		$actual_result = $this->_subject->getFilename($id);
		$this->assertEquals(ROOT . Webcam::ROOT . $id  . '.' . Webcam::EXTENSION, $actual_result);
	}

}
