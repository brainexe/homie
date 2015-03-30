<?php

namespace Tests\Raspberry\Blog\BlogGateway;

use BrainExe\Core\Redis\Predis;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Blog\BlogGateway;
use Raspberry\Blog\BlogPostVO;

/**
 * @covers Raspberry\Blog\BlogGateway
 */
class BlogGatewayTest extends PHPUnit_Framework_TestCase
{

    use RedisMockTrait;

    /**
     * @var BlogGateway
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $mockRedis;

    public function setUp()
    {
        $this->mockRedis = $this->getRedisMock();
        $this->subject   = new BlogGateway();
        $this->subject->setRedis($this->mockRedis);
    }

    public function testGetPosts()
    {
        $userId  = 42;
        $from    = 10;
        $toTime  = 100;

        $key = "blog:$userId";

        $post = new BlogPostVO();
        $postsRaw = [
            serialize($post) => $timestamp = 50
        ];

        $this->mockRedis
            ->expects($this->once())
            ->method('zRangeByScore')
            ->with($key, $from, $toTime, ['withscores' => true])
            ->willReturn($postsRaw);

        $actualResult = $this->subject->getPosts($userId, $from, $toTime);

        $expectedResult = [
            $timestamp => $post
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetRecentPostWithEmptyResult()
    {
        $userId = 0;

        $key = "blog:$userId";

        $postsRaw = [];

        $this->mockRedis
            ->expects($this->once())
            ->method('zRevRangeByScore')
            ->with($key, '+inf', '0', ['limit' => [0, 1]])
            ->willReturn($postsRaw);

        $actualResult = $this->subject->getRecentPost($userId);

        $this->assertNull($actualResult);
    }

    public function testGetRecentPost()
    {
        $userId = 0;

        $key = "blog:$userId";

        $post = new BlogPostVO();
        $postsRaw = [
            serialize($post)
        ];

        $this->mockRedis
            ->expects($this->once())
            ->method('zRevRangeByScore')
            ->with($key, '+inf', '0', ['limit' => [0, 1]])
            ->willReturn($postsRaw);

        $actualResult = $this->subject->getRecentPost($userId);

        $this->assertEquals($post, $actualResult);
    }

    public function testAddSubscriber()
    {
        $userId = 42;
        $targetId = 12;

        $this->mockRedis
            ->expects($this->once())
            ->method('sAdd')
            ->with("blog:subscribers:$targetId", $userId);

        $this->subject->addSubscriber($userId, $targetId);
    }

    public function testGetSubscriber()
    {
        $targetId = 12;

        $subscribers = [];

        $this->mockRedis
            ->expects($this->once())
            ->method('sMembers')
            ->with("blog:subscribers:$targetId")
            ->willReturn($subscribers);

        $actualResult = $this->subject->getSubscriber($targetId);

        $this->assertEquals($subscribers, $actualResult);
    }

    public function testAddPost()
    {
        $userId    = 42;
        $timestamp = 100;
        $postVo    = new BlogPostVO();

        $this->mockRedis
            ->expects($this->once())
            ->method('zAdd')
            ->with("blog:$userId", $timestamp, serialize($postVo));

        $this->subject->addPost($userId, $timestamp, $postVo);
    }

    public function testDeletePost()
    {
        $userId = 42;
        $timestamp = 1000;

        $this->mockRedis
            ->expects($this->once())
            ->method('zDeleteRangeByScore')
            ->with("blog:$userId");

        $this->subject->deletePost($userId, $timestamp);
    }
}
