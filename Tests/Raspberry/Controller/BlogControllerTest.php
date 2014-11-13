<?php

namespace Tests\Raspberry\Controller\BlogController;

use BrainExe\Core\Application\UserException;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Blog\BlogPostVO;
use Raspberry\Controller\BlogController;
use Raspberry\Blog\Blog;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers Raspberry\Controller\BlogController
 */
class BlogControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var BlogController
	 */
	private $_subject;

	/**
	 * @var Blog|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockBlog;

	/**
	 * @var DatabaseUserProvider|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockDatabaseUserProvider;

	public function setUp() {
		$this->_mockBlog = $this->getMock(Blog::class, [], [], '', false);
		$this->_mockDatabaseUserProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);

		$this->_subject = new BlogController($this->_mockBlog, $this->_mockDatabaseUserProvider);
	}

	public function testIndex() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->index($request);
	}

	public function testGetMood() {
		$request = new Request();
		$user_id = 42;
		$recent_post = new BlogPostVO();
		$recent_post->mood = $mood = 3;
		$recent_post->text = $text = 'text';

		$request->attributes->set('user_id', $user_id);

		$this->_mockBlog
			->expects($this->once())
			->method('getRecentPost')
			->with($user_id)
			->will($this->returnValue($recent_post));

		$expected_result = new JsonResponse([
			'mood' => $mood * 10,
			'thought' => $text,
		]);
		$actual_result = $this->_subject->getMood($request);

		$this->assertEquals($expected_result, $actual_result);
	}

	public function testBlogForUser() {
		$request = new Request();
		$user_id = 42;

		$request->attributes->set('user_id', $user_id);

		$posts = [];
		$user_names = [
			'Hans Peter' => $user_id
		];

		$this->_mockBlog
			->expects($this->once())
			->method('getPosts')
			->with($user_id)
			->will($this->returnValue($posts));

		$this->_mockDatabaseUserProvider
			->expects($this->once())
			->method('getAllUserNames')
			->will($this->returnValue($user_names));

		$actual_result = $this->_subject->blogForUser($request, $user_id);

		$expected_result = new JsonResponse([
			'posts' => $posts,
			'users' => $user_names,
			'active_user_id' => $user_id,
			'current_user_id' => $user_id,
		]);

		$this->assertEquals($expected_result, $actual_result);
	}

	/**
	 * @expectedException \BrainExe\Core\Application\UserException
	 * @expectedExceptionMessage User not found: 42
	 */
	public function testBlogForUserWithUndefinedUser() {
		$request = new Request();
		$user_id = 42;

		$request->attributes->set('user_id', $user_id);

		$posts = [];
		$user_names = [
			'Hans Peter' => 100
		];

		$this->_mockBlog
			->expects($this->once())
			->method('getPosts')
			->with($user_id)
			->will($this->returnValue($posts));

		$this->_mockDatabaseUserProvider
			->expects($this->once())
			->method('getAllUserNames')
			->will($this->returnValue($user_names));

		$this->_subject->blogForUser($request, $user_id);
	}

	public function testAddPost() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->addPost($request);
	}

	public function testDeletePost() {
		$user_id = 42;
		$timestamp = 1212;

		$request = new Request();
		$request->attributes->set('user_id', $user_id);

		$this->_mockBlog
			->expects($this->once())
			->method('deletePost')
			->with($user_id, $timestamp);

		$actual_result = $this->_subject->deletePost($request, $timestamp);

		$expected_response = new JsonResponse(true);
		$this->assertEquals($expected_response, $actual_result);
	}

}
