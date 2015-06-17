<?php

namespace Tests\Homie\Webcam;

use League\Flysystem\Filesystem;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Webcam\Controller;
use Homie\Webcam\WebcamEvent;
use Symfony\Component\HttpFoundation\Request;
use Homie\Webcam\Webcam;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\IdGenerator;

/**
 * @covers Homie\Webcam\Controller
 */
class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var Webcam|MockObject
     */
    private $webcam;

    /**
     * @var EventDispatcher|MockObject
     */
    private $eventDispatcher;

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    /**
     * @var Filesystem|MockObject
     */
    private $filesystem;

    public function setUp()
    {
        $this->webcam          = $this->getMock(Webcam::class, [], [], '', false);
        $this->eventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->idGenerator     = $this->getMock(IdGenerator::class, [], [], '', false);
        $this->filesystem      = $this->getMock(Filesystem::class, [], [], '', false);

        $this->subject = new Controller($this->webcam, $this->filesystem);
        $this->subject->setEventDispatcher($this->eventDispatcher);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testIndex()
    {
        $photos = [];

        $this->webcam
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

        $this->idGenerator
            ->expects($this->once())
            ->method('generateRandomId')
            ->willReturn($randomId);

        $event = new WebcamEvent($randomId, WebcamEvent::TAKE_PHOTO);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $actualResult = $this->subject->takePhoto();

        $this->assertEquals(true, $actualResult);
    }

    public function testDelete()
    {
        $photoId = 12;

        $request = new Request();
        $request->request->set('shotId', $photoId);

        $this->webcam
            ->expects($this->once())
            ->method('delete')
            ->with($photoId);

        $actualResult = $this->subject->delete($request);

        $this->assertTrue($actualResult);
    }


    public function testGetImage()
    {
        $request = new Request();
        $request->query->set('file', $file = 'file');
        $stream = fopen(__FILE__, 'r');
        $mime   = 'mime';

        $this->filesystem
            ->expects($this->once())
            ->method('readStream')
            ->with($file)
            ->willReturn($stream);
        $this->filesystem
            ->expects($this->once())
            ->method('getMimeType')
            ->with($file)
            ->willReturn($mime);

        $actualResult = $this->subject->getImage($request);

        $this->assertEquals($mime, $actualResult->headers->get('Content-Type'));
        $this->assertNotEmpty($actualResult->getContent());
    }
}