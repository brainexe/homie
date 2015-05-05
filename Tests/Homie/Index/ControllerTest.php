<?php

namespace Tests\Homie\Index;

use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Index\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers Homie\Index\Controller
 */
class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Controller();
    }

    public function testIndex()
    {
        $this->markTestIncomplete();
        $user = new UserVO();
        $text = 'text';

        $request = new Request();
        $request->attributes->set('user', $user);

        $actualResult = $this->subject->index($request);

        $this->assertEquals(200, $actualResult->getStatusCode());
    }
}
