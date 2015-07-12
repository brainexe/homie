<?php

namespace Homie\Webcam;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Client\ClientInterface;
use League\Flysystem\Filesystem;

/**
 * @Service(public=false)
 */
class Webcam
{
    const ROOT       = 'Webcam/';
    const PICTURE_EXTENSION  = 'jpg';

    use EventDispatcherTrait;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @Inject({"@RemoteFilesystem"})
     * @param Filesystem $fileUploader
     */
    public function __construct(Filesystem $fileUploader)
    {
        $this->filesystem = $fileUploader;
    }

    /**
     * @todo get all files
     * @return WebcamVO[]
     */
    public function getPhotos()
    {
        $files = $this->filesystem->listContents(self::ROOT, true);

        $vos = [];
        foreach ($files as $file) {
            $webcamVo = $vos[]   = new WebcamVO();
            $webcamVo->filePath  = $file['path'];
            $webcamVo->name      = $file['basename'];
            $webcamVo->webcamId  = $file['basename'];
            $webcamVo->webPath   = sprintf('%s%s', self::ROOT, $webcamVo->name);
            $webcamVo->timestamp = isset($file['timestamp']) ? $file['timestamp'] : null;
        }

        return $vos;
    }

    /**
     * @param string $filename
     */
    public function delete($filename)
    {
        $this->filesystem->delete($filename);
    }
}
