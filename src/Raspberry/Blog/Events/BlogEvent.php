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
    public $userVo;

    /**
     * @param UserVO $userVo
     * @param BlogPostVO $post
     */
    public function __construct(UserVO $userVo, BlogPostVO $post)
    {
        $this->event_name = self::POST;
        $this->post       = $post;
        $this->userVo     = $userVo;
    }
}
