<?php

namespace Tests\Raspberry\Controller\RadioController;

use BrainExe\Core\Controller\ControllerInterface;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Controller\RadioController;
use Raspberry\Radio\RadioChangeEvent;
use Raspberry\Radio\VO\RadioVO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Raspberry\Radio\Radios;
use Raspberry\Radio\RadioJob;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\Controller\RadioController
 */
class RadioControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RadioController
     */
    private $subject;

    /**
     * @var Radios|MockObject
     */
    private $mockRadios;

    /**
     * @var RadioJob|MockObject
     */
    private $mockRadioJob;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockDispatcher;

    public function setUp()
    {
        $this->mockRadios     = $this->getMock(Radios::class, [], [], '', false);
        $this->mockRadioJob   = $this->getMock(RadioJob::class, [], [], '', false);
        $this->mockDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new RadioController($this->mockRadios, $this->mockRadioJob);
        $this->subject->setEventDispatcher($this->mockDispatcher);
    }

    public function testIndex()
    {
        $radiosFormatted = ['radios_formatted'];
        $jobs = ['jobs'];

        $this->mockRadios
            ->expects($this->once())
            ->method('getRadios')
            ->willReturn($radiosFormatted);

        $this->mockRadioJob
            ->expects($this->once())
            ->method('getJobs')
            ->willReturn($jobs);

        $actualResult = $this->subject->index();

        $expectedResult = [
            'radios' => $radiosFormatted,
            'radio_jobs' => $jobs,
            'pins' => Radios::$radioPins,
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testSetStatus()
    {
        $request  = new Request();
        $radioId  = 10;
        $status   = true;
        $radioVo  = new RadioVO();
        $event    = new RadioChangeEvent($radioVo, $status);

        $this->mockRadios
            ->expects($this->once())
            ->method('getRadio')
            ->with($radioId)
            ->willReturn($radioVo);

        $this->mockDispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $actualResult = $this->subject->setStatus($request, $radioId, $status);

        $expectedResult = new JsonResponse(true);
        $expectedResult->headers->set('X-Flash', json_encode([ControllerInterface::ALERT_SUCCESS, _('Set Radio')]));

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAddRadio()
    {
        $name        = 'name';
        $description = 'description';
        $code        = 12;
        $pinRaw      = 'A';
        $pin         = 1;

        $request = new Request();
        $request->request->set('name', $name);
        $request->request->set('description', $description);
        $request->request->set('code', $code);
        $request->request->set('pin', $pinRaw);

        $radioVo = new RadioVO();
        $radioVo->name        = $name;
        $radioVo->description = $description;
        $radioVo->code        = $code;
        $radioVo->pin         = $pin;

        $this->mockRadios
            ->expects($this->once())
            ->method('addRadio')
            ->with($radioVo);

        $this->mockRadios
            ->expects($this->once())
            ->method('getRadioPin')
            ->with($pinRaw)
            ->willReturn($pin);

        $actualResult = $this->subject->addRadio($request);

        $this->assertEquals($radioVo, $actualResult);
    }

    public function testDeleteRadio()
    {
        $request = new Request();
        $radioId = 10;

        $this->mockRadios
            ->expects($this->once())
            ->method('deleteRadio')
            ->with($radioId);

        $actualResult = $this->subject->deleteRadio($request, $radioId);

        $this->assertTrue($actualResult);
    }


    public function testAddRadioJob()
    {
        $radioId    = 10;
        $status     = false;
        $timeString = 'time';

        $radioVo = new RadioVO();

        $request = new Request();
        $request->request->set('radio_id', $radioId);
        $request->request->set('status', $status);
        $request->request->set('time', $timeString);

        $this->mockRadios
            ->expects($this->once())
            ->method('getRadio')
            ->with($radioId)
            ->willReturn($radioVo);

        $this->mockRadioJob
            ->expects($this->once())
            ->method('addRadioJob')
            ->with($radioVo, $timeString, $status);

        $actualResult = $this->subject->addRadioJob($request);

        $expectedResult = new JsonResponse(true);
        $expectedResult->headers->set(
            'X-Flash',
            json_encode([ControllerInterface::ALERT_SUCCESS, _('The job was sored successfully')])
        );
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testDeleteRadioJob()
    {
        $request = new Request();
        $radioId = 10;

        $this->mockRadioJob
            ->expects($this->once())
            ->method('deleteJob')
            ->with($radioId);

        $actualResult = $this->subject->deleteRadioJob($request, $radioId);

        $this->assertTrue($actualResult);
    }
}
