<?php

namespace Tests\Raspberry\Blog\Blog;

use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Blog\Blog;
use Raspberry\Blog\BlogGateway;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\Time;
use Raspberry\Blog\BlogPostVO;
use Raspberry\Blog\Events\BlogEvent;

/**
 * @Covers Raspberry\Blog\Blog
 */
class BlogTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Blog
	 */
	private $subject;

	/**
	 * @var BlogGateway|MockObject
	 */
	private $mockBlogGateway;

	/**
	 * @var EventDispatcher|MockObject
	 */
	private $mockEventDispatcher;

	/**
	 * @var Time|MockObject
	 */
	private $_mockTime;

	public function setUp() {
		$this->mockBlogGateway = $this->getMock(BlogGateway::class, [], [], '', false);
		$this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
		$this->_mockTime = $this->getMock(Time::class, [], [], '', false);

		$this->subject = new Blog($this->mockBlogGateway);
		$this->subject->setEventDispatcher($this->mockEventDispatcher);
		$this->subject->setTime($this->_mockTime);
	}

	public function testGetPosts() {
		$user_id = 42;
		$from = 10;
		$to = 20;

		$posts = [];

		$this->mockBlogGateway
			->expects($this->once())
			->method('getPosts')
			->with($user_id, $from, $to)
			->will($this->returnValue($posts));

		$actual_result = $this->subject->getPosts($user_id, $from, $to);

		$this->assertEquals($posts, $actual_result);
	}

	/**
	 *
	 */
	public function testAddPost() {
		$now = 1000;

		$post_vo = new BlogPostVO();
		$post_vo->mood = -1;
		$user = new UserVO();
		$user->id = $user_id = 42;

		$expected_post = clone $post_vo;
		$expected_post->mood = null;

		$this->_mockTime
			->expects($this->once())
			->method('now')
			->will($this->returnValue($now));

		$this->mockBlogGateway
			->expects($this->once())
			->method('addPost')
			->with($user_id, $now, $post_vo);

		$event = new BlogEvent($user, $expected_post);
		$this->mockEventDispatcher
			->expects($this->once())
			->method('dispatchInBackground')
			->with($event);

		$this->subject->addPost($user, $post_vo);
	}

	public function testDeletePost() {
		$user_id = 42;
		$timestamp = 1000;

		$this->mockBlogGateway
			->expects($this->once())
			->method('deletePost')
			->with($user_id, $timestamp);

		$this->subject->deletePost($user_id, $timestamp);
	}

	public function testAddSubscriber() {
		$user_id = 42;
		$target_id = 24;

		$this->mockBlogGateway
			->expects($this->once())
			->method('addSubscriber')
			->with($user_id, $target_id);

		$this->subject->addSubscriber($user_id, $target_id);
	}

	public function testGetRecentPost() {
		$user_id = 42;

		$post_vo = new BlogPostVO();

		$this->mockBlogGateway
			->expects($this->once())
			->method('getRecentPost')
			->with($user_id)
			->will($this->returnValue($post_vo));

		$actual_result = $this->subject->getRecentPost($user_id);

		$this->assertEquals($post_vo, $actual_result);
	}

}
