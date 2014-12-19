<?php

namespace Tests\Raspberry\Controller\WebcamController;

use BrainExe\Core\Controller\ControllerInterface;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Controller\WebcamController;
use Raspberry\Webcam\WebcamEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Raspberry\Webcam\Webcam;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\IdGenerator;

/**
 * @Covers Raspberry\Controller\WebcamController
 */
class WebcamControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var WebcamController
     */
    private $_subject;

    /**
     * @var Webcam|MockObject
     */
    private $_mockWebcam;

    /**
     * @var EventDispatcher|MockObject
     */
    private $_mockEventDispatcher;

    /**
     * @var IdGenerator|MockObject
     */
    private $_mockIdGenerator;

    public function setUp()
    {
        $this->_mockWebcam = $this->getMock(Webcam::class, [], [], '', false);
        $this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->_mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);

        $this->_subject = new WebcamController($this->_mockWebcam);
        $this->_subject->setEventDispatcher($this->_mockEventDispatcher);
        $this->_subject->setIdGenerator($this->_mockIdGenerator);
    }

    public function testIndex()
    {
        $photos = [];

        $this->_mockWebcam
        ->expects($this->once())
        ->method('getPhotos')
        ->will($this->returnValue($photos));

        $actual_result = $this->_subject->index();

        $expected_result = [
        'shots' => $photos
        ];

        $this->assertEquals($expected_result, $actual_result);
    }

    public function testTakePhoto()
    {
        $random_id = 11880;

        $this->_mockIdGenerator
        ->expects($this->once())
        ->method('generateRandomId')
        ->will($this->returnValue($random_id));

        $event = new WebcamEvent($random_id, WebcamEvent::TAKE_PHOTO);

        $this->_mockEventDispatcher
        ->expects($this->once())
        ->method('dispatchInBackground')
        ->with($event);

        $actual_result = $this->_subject->takePhoto();

        $expected_result = new JsonResponse(true);
     // todo this->anything()
        $expected_result->headers->set('X-Flash', json_encode([ControllerInterface::ALERT_INFO, 'Cheese...']));

        $this->assertEquals($expected_result, $actual_result);
    }

    public function testDelete()
    {
        $photo_id = 12;

        $request = new Request();
        $request->request->set('shot_id', $photo_id);

        $this->_mockWebcam
        ->expects($this->once())
        ->method('delete')
        ->with($photo_id);


        $actual_result = $this->_subject->delete($request);

        $this->assertTrue($actual_result);
    }
}
