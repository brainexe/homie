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
        $this->mockRadios = $this->getMock(Radios::class, [], [], '', false);
        $this->mockRadioJob = $this->getMock(RadioJob::class, [], [], '', false);
        $this->mockDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new RadioController($this->mockRadios, $this->mockRadioJob);
        $this->subject->setEventDispatcher($this->mockDispatcher);
    }

    public function testIndex()
    {
        $radios_formatted = ['radios_formatted'];
        $jobs = ['jobs'];

        $this->mockRadios
            ->expects($this->once())
            ->method('getRadios')
            ->will($this->returnValue($radios_formatted));

        $this->mockRadioJob
            ->expects($this->once())
            ->method('getJobs')
            ->will($this->returnValue($jobs));

        $actualResult = $this->subject->index();

        $expectedResult = [
            'radios' => $radios_formatted,
            'radio_jobs' => $jobs,
            'pins' => Radios::$radioPins,
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testSetStatus()
    {
        $request  = new Request();
        $radio_id = 10;
        $status   = true;
        $radio_vo = new RadioVO();
        $event    = new RadioChangeEvent($radio_vo, $status);

        $this->mockRadios
            ->expects($this->once())
            ->method('getRadio')
            ->with($radio_id)
            ->will($this->returnValue($radio_vo));

        $this->mockDispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $actualResult = $this->subject->setStatus($request, $radio_id, $status);

        $expectedResult = new JsonResponse(true);
        $expectedResult->headers->set('X-Flash', json_encode([ControllerInterface::ALERT_SUCCESS, _('Set Radio')]));

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAddRadio()
    {
        $name        = 'name';
        $description = 'description';
        $code        = 12;
        $pin_raw     = 'A';
        $pin         = 1;

        $request = new Request();
        $request->request->set('name', $name);
        $request->request->set('description', $description);
        $request->request->set('code', $code);
        $request->request->set('pin', $pin_raw);

        $radio_vo = new RadioVO();
        $radio_vo->name        = $name;
        $radio_vo->description = $description;
        $radio_vo->code        = $code;
        $radio_vo->pin         = $pin;

        $this->mockRadios
        ->expects($this->once())
        ->method('addRadio')
        ->with($radio_vo);

        $this->mockRadios
        ->expects($this->once())
        ->method('getRadioPin')
        ->with($pin_raw)
        ->will($this->returnValue($pin));

        $actualResult = $this->subject->addRadio($request);

        $this->assertEquals($radio_vo, $actualResult);
    }

    public function testDeleteRadio()
    {
        $request = new Request();
        $radio_id = 10;

        $this->mockRadios
        ->expects($this->once())
        ->method('deleteRadio')
        ->with($radio_id);

        $actualResult = $this->subject->deleteRadio($request, $radio_id);

        $this->assertTrue($actualResult);
    }

    public function testEditRadio()
    {
        $radio_id = 10;

        $request = new Request();
        $request->request->set('radio_id', $radio_id);

        $actualResult = $this->subject->editRadio($request);

        $this->assertTrue($actualResult);
    }

    public function testAddRadioJob()
    {
        $radio_id    = 10;
        $status      = false;
        $time_string = 'time';

        $radio_vo = new RadioVO();

        $request = new Request();
        $request->request->set('radio_id', $radio_id);
        $request->request->set('status', $status);
        $request->request->set('time', $time_string);

        $this->mockRadios
            ->expects($this->once())
            ->method('getRadio')
            ->with($radio_id)
            ->will($this->returnValue($radio_vo));

        $this->mockRadioJob
            ->expects($this->once())
            ->method('addRadioJob')
            ->with($radio_vo, $time_string, $status);

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
        $radio_id = 10;

        $this->mockRadioJob
            ->expects($this->once())
            ->method('deleteJob')
            ->with($radio_id);

        $actualResult = $this->subject->deleteRadioJob($request, $radio_id);

        $this->assertTrue($actualResult);
    }
}
