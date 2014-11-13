<?php

namespace Tests\Raspberry\Controller\WebcamController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Controller\WebcamController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Raspberry\Webcam\Webcam;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\IdGenerator;

/**
 * @Covers Raspberry\Controller\WebcamController
 */
class WebcamControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var WebcamController
	 */
	private $_subject;

	/**
	 * @var Webcam|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockWebcam;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;

	/**
	 * @var IdGenerator|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockIdGenerator;

	public function setUp() {
		$this->_mockWebcam = $this->getMock(Webcam::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
		$this->_mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);

		$this->_subject = new WebcamController($this->_mockWebcam);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
		$this->_subject->setIdGenerator($this->_mockIdGenerator);
	}

	public function testIndex() {
		$photos = [];

		$this->_mockWebcam
			->expects($this->once())
			->method('getPhotos')
			->will($this->returnValue($photos));

		$expected_result = new JsonResponse([
			'shots' => $photos
		]);

		$actual_result = $this->_subject->index();

		$this->assertEquals($expected_result, $actual_result);
	}

	public function testTakePhoto() {
		$this->markTestIncomplete('This is only a dummy implementation');


		$this->_subject->takePhoto();
	}

	public function testDelete() {
		$photo_id = 12;

		$request = new Request();
		$request->request->set('shot_id', $photo_id);

		$this->_mockWebcam
			->expects($this->once())
			->method('delete')
			->with($photo_id);

		$expected_result = new JsonResponse(true);

		$actual_result = $this->_subject->delete($request);

		$this->assertEquals($expected_result, $actual_result);
	}

}
