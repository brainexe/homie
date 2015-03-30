<?php

namespace Tests\Raspberry\Webcam;

use BrainExe\Core\Controller\ControllerInterface;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Webcam\Controller;
use Raspberry\Webcam\WebcamEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Raspberry\Webcam\Webcam;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\IdGenerator;

/**
 * @covers Raspberry\Webcam\Controller
 */
class ControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var Webcam|MockObject
     */
    private $mockWebcam;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    /**
     * @var IdGenerator|MockObject
     */
    private $mockIdGenerator;

    public function setUp()
    {
        $this->mockWebcam = $this->getMock(Webcam::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);

        $this->subject = new Controller($this->mockWebcam);
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
        $this->subject->setIdGenerator($this->mockIdGenerator);
    }

    public function testIndex()
    {
        $photos = [];

        $this->mockWebcam
            ->expects($this->once())
            ->method('getPhotos')
            ->willReturn($photos);

        $actualResult = $this->subject->index();

        $expectedResult = [
                'shots' => $photos
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testTakePhoto()
    {
        $randomId = 11880;

        $this->mockIdGenerator
            ->expects($this->once())
            ->method('generateRandomId')
            ->willReturn($randomId);

        $event = new WebcamEvent($randomId, WebcamEvent::TAKE_PHOTO);

        $this->mockEventDispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $actualResult = $this->subject->takePhoto();

        $expectedResult = new JsonResponse(true);
     // todo this->anything()
        $expectedResult->headers->set('X-Flash', json_encode([ControllerInterface::ALERT_INFO, 'Cheese...']));

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testDelete()
    {
        $photoId = 12;

        $request = new Request();
        $request->request->set('shot_id', $photoId);

        $this->mockWebcam
            ->expects($this->once())
            ->method('delete')
            ->with($photoId);

        $actualResult = $this->subject->delete($request);

        $this->assertTrue($actualResult);
    }
}
