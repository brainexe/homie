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
    public function getFiles() : array
    {
        $files = $this->filesystem->listContents(self::ROOT, true);

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
        $files = $this->filesystem->listContents(self::ROOT, true);

        if (empty($files)) {
            return null;
        }

        usort($files, function (array $a, array $b) {
            return @$a['timestamp'] <=> @$b['timestamp'];
        });
        $file = array_pop($files);

        return $this->formatFile($file);
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
