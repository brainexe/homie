<?php

namespace Tests\Raspberry\Index;

use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Index\Controller;
use Symfony\Component\HttpFoundation\Request;
use BrainExe\Template\TwigEnvironment;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Covers Raspberry\Index
 */
class ControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var TwigEnvironment|MockObject
     */
    private $twig;

    public function setUp()
    {
        $this->twig = $this->getMock(TwigEnvironment::class, [], [], '', false);

        $this->subject = new Controller();
        $this->subject->setTwig($this->twig);
    }

    public function testIndex()
    {
        $user = new UserVO();
        $text = 'text';

        $request = new Request();
        $request->attributes->set('user', $user);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with('layout.html.twig', [
                'current_user' => $user
            ])
            ->willReturn($text);

        $actualResult = $this->subject->index($request);

        $expectedResult = new Response($text);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
