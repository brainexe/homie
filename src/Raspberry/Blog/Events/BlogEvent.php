<?php

namespace Raspberry\Blog\Events;

use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\EventDispatcher\AbstractEvent;
use Raspberry\Blog\BlogPostVO;

class BlogEvent extends AbstractEvent
{
    const POST = 'blog.post';

    /**
     * @var BlogPostVO
     */
    public $post;

    /**
     * @var UserVO
     */
    public $user_vo;

    /**
     * @param UserVO $user_vo
     * @param BlogPostVO $post
     */
    public function __construct(UserVO $user_vo, BlogPostVO $post)
    {
        $this->event_name = self::POST;
        $this->post = $post;
        $this->user_vo = $user_vo;
    }
}
