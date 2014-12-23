<?php

namespace Tests\Raspberry\Controller\GpioController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

use Raspberry\Controller\GpioController;
use Raspberry\Gpio\Pin;
use Raspberry\Gpio\PinsCollection;

use Symfony\Component\HttpFoundation\Request;
use Raspberry\Gpio\GpioManager;

/**
 * @Covers Raspberry\Controller\GpioController
 */
class GpioControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var GpioController
     */
    private $subject;

    /**
     * @var GpioManager|MockObject
     */
    private $mockGpioManager;

    public function setUp()
    {
        $this->mockGpioManager = $this->getMock(GpioManager::class, [], [], '', false);

        $this->subject = new GpioController($this->mockGpioManager);
    }

    public function testIndex()
    {
        $pin  = new Pin();
        $pins = new PinsCollection();
        $pins->add($pin);

        $this->mockGpioManager
            ->expects($this->once())
            ->method('getPins')
            ->willReturn($pins);

        $actualResult = $this->subject->index();

        $expectedResult = [
            'pins' => $pins->getAll()
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testSetStatus()
    {
        $request = new Request();
        $gpioId  = 10;
        $status  = true;
        $value   = false;
        $pin     = new Pin();

        $this->mockGpioManager
            ->expects($this->once())
            ->method('setPin')
            ->with($gpioId, $status, $value)
            ->willReturn($pin);

        $actualResult = $this->subject->setStatus($request, $gpioId, $status, $value);

        $this->assertEquals($pin, $actualResult);
    }
}
