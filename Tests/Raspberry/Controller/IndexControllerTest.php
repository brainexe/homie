<?php

namespace Tests\Raspberry\Controller\IndexController;

use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;

use Raspberry\Controller\IndexController;
use Symfony\Component\HttpFoundation\Request;
use BrainExe\Template\TwigEnvironment;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Covers Raspberry\Controller\IndexController
 */
class IndexControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var IndexController
	 */
	private $_subject;

	/**
	 * @var TwigEnvironment|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockTwigEnvironment;

	public function setUp() {
		$this->_mockTwigEnvironment = $this->getMock(TwigEnvironment::class, [], [], '', false);

		$this->_subject = new IndexController();
		$this->_subject->setTwig($this->_mockTwigEnvironment);
	}

	public function testIndex() {
		$user = new UserVO();
		$text = 'text';

		$request = new Request();
		$request->attributes->set('user', $user);

		$this->_mockTwigEnvironment
			->expects($this->once())
			->method('render')
			->with('layout.html.twig', [
				'current_user' => $user
			])
			->will($this->returnValue($text));

		$actual_result = $this->_subject->index($request);

		$expected_result = new Response($text);

		$this->assertEquals($expected_result, $actual_result);
	}

}
