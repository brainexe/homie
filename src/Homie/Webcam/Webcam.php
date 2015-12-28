<?php

namespace Homie\Webcam;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;

use League\Flysystem\Filesystem;

/**
 * @Service(public=false)
 */
class Webcam
{
    const ROOT = 'Webcam/';

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
     * @return WebcamVO[]
     */
    public function getFiles()
    {
        $files = $this->filesystem->listContents(self::ROOT, true);

        $vos = [];
        foreach ($files as $file) {
            $vos[] = $this->formatFile($file);
        }

        return $vos;
    }

    /**
     * @return WebcamVO
     */
    public function getRecentImage()
    {
        $files = $this->filesystem->listContents(self::ROOT, true);

        if (empty($files)) {
            return [];
        }

        usort($files, function (array $a, array $b) {
            // todo check via isset()
            return @$a['timestamp'] > @$b['timestamp'];
        });
        $file = array_pop($files);

        return $this->formatFile($file);
    }

    /**
     * @param string $filename
     * @return bool
     */
    public function delete($filename)
    {
        return $this->filesystem->delete($filename);
    }

    /**
     * @param array $file
     * @return WebcamVO
     */
    private function formatFile(array $file)
    {
        $fileVo = new WebcamVO();
        $fileVo->filePath  = $file['path'];
        $fileVo->name      = $file['basename'];
        $fileVo->webcamId  = $file['basename'];
        $fileVo->extension = isset($file['extension']) ? $file['extension'] : '';
        $fileVo->webPath   = sprintf('%s%s', self::ROOT, $fileVo->name);
        $fileVo->timestamp = isset($file['timestamp']) ? $file['timestamp'] : null;

        return $fileVo;
    }
}
