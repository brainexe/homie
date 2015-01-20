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
class BlogTest extends PHPUnit_Framework_TestCase
{

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
    private $mockTime;

    public function setUp()
    {
        $this->mockBlogGateway = $this->getMock(BlogGateway::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->mockTime = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new Blog($this->mockBlogGateway);
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
        $this->subject->setTime($this->mockTime);
    }

    public function testGetPosts()
    {
        $userId = 42;
        $from = 10;
        $to = 20;

        $posts = [];

        $this->mockBlogGateway
            ->expects($this->once())
            ->method('getPosts')
            ->with($userId, $from, $to)
            ->willReturn($posts);

        $actualResult = $this->subject->getPosts($userId, $from, $to);

        $this->assertEquals($posts, $actualResult);
    }

    /**
     *
     */
    public function testAddPost()
    {
        $now = 1000;

        $postVo = new BlogPostVO();
        $postVo->mood = -1;
        $user = new UserVO();
        $user->id = $userId = 42;

        $expected_post = clone $postVo;
        $expected_post->mood = null;

        $this->mockTime
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $this->mockBlogGateway
            ->expects($this->once())
            ->method('addPost')
            ->with($userId, $now, $postVo);

        $event = new BlogEvent($user, $expected_post);
        $this->mockEventDispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $this->subject->addPost($user, $postVo);
    }

    public function testDeletePost()
    {
        $userId = 42;
        $timestamp = 1000;

        $this->mockBlogGateway
            ->expects($this->once())
            ->method('deletePost')
            ->with($userId, $timestamp);

        $this->subject->deletePost($userId, $timestamp);
    }

    public function testAddSubscriber()
    {
        $userId = 42;
        $target_id = 24;

        $this->mockBlogGateway
            ->expects($this->once())
            ->method('addSubscriber')
            ->with($userId, $target_id);

        $this->subject->addSubscriber($userId, $target_id);
    }

    public function testGetRecentPost()
    {
        $userId = 42;

        $postVo = new BlogPostVO();

        $this->mockBlogGateway
            ->expects($this->once())
            ->method('getRecentPost')
            ->with($userId)
            ->willReturn($postVo);

        $actualResult = $this->subject->getRecentPost($userId);

        $this->assertEquals($postVo, $actualResult);
    }
}
