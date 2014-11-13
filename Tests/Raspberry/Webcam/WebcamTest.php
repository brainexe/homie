<?php

namespace Tests\Raspberry\Webcam\Webcam;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Webcam\Webcam;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Finder\Finder;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\Webcam\Webcam
 */
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
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getPhotos();
	}

	public function testTakePhoto() {
		$this->markTestIncomplete('This is only a dummy implementation');

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
