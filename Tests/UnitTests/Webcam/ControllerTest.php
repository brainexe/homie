<?php

namespace Tests\Homie\Webcam;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\IdGenerator;
use Homie\Webcam\Controller;
use Homie\Webcam\Webcam;
use Homie\Webcam\WebcamEvent;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \Homie\Webcam\Controller
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
        $this->webcam          = $this->createMock(Webcam::class);
        $this->eventDispatcher = $this->createMock(EventDispatcher::class);
        $this->idGenerator     = $this->createMock(IdGenerator::class);
        $this->filesystem      = $this->createMock(Filesystem::class);

        $this->subject = new Controller($this->webcam, $this->filesystem);
        $this->subject->setEventDispatcher($this->eventDispatcher);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testIndex()
    {
        $photos = [];

        $this->webcam
            ->expects($this->once())
            ->method('getFiles')
            ->willReturn($photos);

        $actual = $this->subject->index();

        $expected = [
            'files' => $photos
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testGetRecent()
    {
        $photos = [];

        $this->webcam
            ->expects($this->once())
            ->method('getRecentImage')
            ->willReturn($photos);

        $actual = $this->subject->loadRecent();

        $this->assertEquals($photos, $actual);
    }

    /**
     * @dataProvider provideTakeActions
     * @param WebcamEvent $expectedEvent
     * @param string $randomId
     * @param string $type
     */
    public function testTakePhoto(WebcamEvent $expectedEvent, $randomId, $type)
    {
        $request = new Request();

        $this->idGenerator
            ->expects($this->once())
            ->method('generateUniqueId')
            ->willReturn($randomId);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($expectedEvent);

        $request->request->set('duration', 5);
        $actualResult = $this->subject->take($request, $type);

        $this->assertEquals(true, $actualResult);
    }

    public function provideTakeActions()
    {
        $randomId = 11880;

        return [
            'photo' => [new WebcamEvent($randomId, WebcamEvent::TAKE_PHOTO), $randomId, 'photo'],
            'video' => [new WebcamEvent($randomId, WebcamEvent::TAKE_VIDEO, 5), $randomId, 'video'],
            'sound' => [new WebcamEvent($randomId, WebcamEvent::TAKE_SOUND, 5), $randomId, 'sound'],
        ];
    }

    public function testDelete()
    {
        $file = "file.png";

        $request = new Request();

        $this->webcam
            ->expects($this->once())
            ->method('delete')
            ->with($file)
            ->willReturn(true);

        $actualResult = $this->subject->delete($request, $file);

        $this->assertTrue($actualResult);
    }


    public function testGetFile()
    {
        $request = new Request();
        $stream = fopen(__FILE__, 'r');
        $mime   = 'mime';
        $file   = 'file';

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

        $actualResult = $this->subject->getFile($request, $file);

        $this->assertEquals($mime, $actualResult->headers->get('Content-Type'));
        $this->assertNotEmpty($actualResult->getContent());
    }
}
