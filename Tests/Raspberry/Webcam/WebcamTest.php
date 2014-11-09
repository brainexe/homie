<?php

namespace Tests\Raspberry\Webcam\Webcam;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Webcam\Webcam;
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
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;


	public function setUp() {
		parent::setUp();

		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new Webcam();
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
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->delete($id);
	}

	public function testGetFilename() {
		$id = '5';

		$actual_result = $this->_subject->getFilename($id);
		$this->assertEquals(ROOT . Webcam::ROOT . $id  . '.' . Webcam::EXTENSION, $actual_result);
	}

}
