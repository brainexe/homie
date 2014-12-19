<?php

namespace Raspberry\Webcam;

class WebcamVO
{

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $file_path;

    /**
     * @var string
     */
    public $web_path;

    /**
     * @varstring
     */
    public $name;

    /**
     * @var string
     */
    public $timestamp;

    /**
     * @return string
     */
    public function getId()
    {
        return basename($this->name, '.' . Webcam::EXTENSION);
    }
}
