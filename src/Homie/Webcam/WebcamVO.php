<?php

namespace Homie\Webcam;

class WebcamVO
{

    /**
     * @var string
     */
    public $webcamId;

    /**
     * @var string
     */
    public $filePath;

    /**
     * @var string
     */
    public $webPath;

    /**
     * @varstring
     */
    public $name;

    /**
     * @var string
     */
    public $timestamp;

    /**
     * @var string
     */
    public $extension;

    /**
     * @return string
     */
    public function getWebcamId()
    {
        return basename($this->name, '.' . Webcam::PICTURE_EXTENSION);
    }
}
