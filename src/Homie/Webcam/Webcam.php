<?php

namespace Homie\Webcam;


use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;

use League\Flysystem\Filesystem;

/**
 * @Service
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
     * @param Filesystem $fileUploader
     */
    public function __construct(Filesystem $fileUploader)
    {
        $this->filesystem = $fileUploader;
    }

    /**
     * @return WebcamVO[]
     */
    public function getFiles() : array
    {
        $files = $this->loadRawFiles();

        $vos = [];
        foreach ($files as $file) {
            $vos[] = $this->formatFile($file);
        }

        return $vos;
    }

    /**
     * @return WebcamVO|null
     */
    public function getRecentImage()
    {
        $files = $this->loadRawFiles();
        if (!$files) {
            return null;
        }

        $first = reset($files);

        return $this->formatFile($first);
    }

    /**
     * @param string $filename
     * @return bool
     */
    public function delete(string $filename) : bool
    {
        return (bool)$this->filesystem->delete($filename);
    }

    /**
     * @return array[]
     */
    private function loadRawFiles() : array
    {
        $files = (array)$this->filesystem->listContents(self::ROOT, true);

        usort($files, function (array $a, array $b) {
            return @$b['timestamp'] <=> @$a['timestamp'];
        });

        return $files;
    }

    /**
     * @param array $file
     * @return WebcamVO
     */
    private function formatFile(array $file) : WebcamVO
    {
        $fileVo = new WebcamVO();
        $fileVo->name      = $file['basename'];
        $fileVo->extension = $file['extension'] ?? '';
        $fileVo->timestamp = $file['timestamp'] ?? null;
        $fileVo->webPath   = sprintf('%s%s', self::ROOT, $file['basename']);

        return $fileVo;
    }
}
