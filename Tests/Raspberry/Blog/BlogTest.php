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
    private $blogGateway;

    /**
     * @var EventDispatcher|MockObject
     */
    private $eventDispatcher;

    /**
     * @var Time|MockObject
     */
    private $time;

    public function setUp()
    {
        $this->blogGateway = $this->getMock(BlogGateway::class, [], [], '', false);
        $this->eventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->time = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new Blog($this->blogGateway);
        $this->subject->setEventDispatcher($this->eventDispatcher);
        $this->subject->setTime($this->time);
    }

    public function testGetPosts()
    {
        $userId = 42;
        $from = 10;
        $timeTo = 20;

        $posts = [];

        $this->blogGateway
            ->expects($this->once())
            ->method('getPosts')
            ->with($userId, $from, $timeTo)
            ->willReturn($posts);

        $actualResult = $this->subject->getPosts($userId, $from, $timeTo);

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

        $expectedPost = clone $postVo;
        $expectedPost->mood = null;

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $this->blogGateway
            ->expects($this->once())
            ->method('addPost')
            ->with($userId, $now, $postVo);

        $event = new BlogEvent($user, $expectedPost);
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $this->subject->addPost($user, $postVo);
    }

    public function testDeletePost()
    {
        $userId = 42;
        $timestamp = 1000;

        $this->blogGateway
            ->expects($this->once())
            ->method('deletePost')
            ->with($userId, $timestamp);

        $this->subject->deletePost($userId, $timestamp);
    }

    public function testAddSubscriber()
    {
        $userId = 42;
        $targetId = 24;

        $this->blogGateway
            ->expects($this->once())
            ->method('addSubscriber')
            ->with($userId, $targetId);

        $this->subject->addSubscriber($userId, $targetId);
    }

    public function testGetRecentPost()
    {
        $userId = 42;

        $postVo = new BlogPostVO();

        $this->blogGateway
            ->expects($this->once())
            ->method('getRecentPost')
            ->with($userId)
            ->willReturn($postVo);

        $actualResult = $this->subject->getRecentPost($userId);

        $this->assertEquals($postVo, $actualResult);
    }
}
