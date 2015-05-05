<?php

namespace Homie\Blog;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\TimeTrait;
use Homie\Blog\Events\BlogEvent;

/**
 * @Service(public=false)
 */
class Blog
{

    use EventDispatcherTrait;
    use TimeTrait;

    /**
     * @var BlogGateway
     */
    private $gateway;

    /**
     * @Inject("@BlogGateway")
     * @param BlogGateway $gateway
     */
    public function __construct(BlogGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param integer $userId
     * @param string $fromTime
     * @param string $toTime
     * @return BlogPostVO[]
     */
    public function getPosts($userId, $fromTime = '0', $toTime = '+inf')
    {
        return $this->gateway->getPosts($userId, $fromTime, $toTime);
    }

    /**
     * @param UserVO $user
     * @param BlogPostVO $post
     */
    public function addPost(UserVO $user, BlogPostVO $post)
    {
        if ($post->mood < BlogPostVO::MIN_MOOD || $post->mood > BlogPostVO::MAX_MOOD) {
            $post->mood = null;
        }

        $this->gateway->addPost($user->id, $this->now(), $post);

        $event = new BlogEvent($user, $post);
        $this->dispatchInBackground($event);
    }

    /**
     * @param integer $userId
     * @param integer $timestamp
     */
    public function deletePost($userId, $timestamp)
    {
        $this->gateway->deletePost($userId, $timestamp);
    }

    /**
     * @param integer $userId
     * @param integer $targetId
     */
    public function addSubscriber($userId, $targetId)
    {
        $this->gateway->addSubscriber($userId, $targetId);
    }

    /**
     * @param integer $userId
     * @return null|BlogPostVO
     */
    public function getRecentPost($userId)
    {
        return $this->gateway->getRecentPost($userId);
    }
}
