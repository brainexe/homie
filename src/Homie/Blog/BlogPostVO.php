<?php

namespace Homie\Blog;

class BlogPostVO
{

    const MIN_MOOD = 1;
    const MAX_MOOD = 10;

    /**
     * @var string
     */
    public $text;

    /**
     * @var integer 1-10
     */
    public $mood;
}
