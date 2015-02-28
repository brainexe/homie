<?php

namespace Raspberry\Webcam;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Service(public=false)
 */
class Webcam
{

    const ROOT       = 'web/static/webcam/';
    const EXTENSION  = 'jpg';
    const TIMEOUT    = 10000;
    const EXECUTABLE = 'fswebcam';

    use EventDispatcherTrait;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var ProcessBuilder
     */
    private $processBuilder;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @Inject({"@Filesystem", "@ProcessBuilder", "@Finder"})
     * @param Filesystem $filesystem
     * @param ProcessBuilder $processBuilder
     * @param Finder $finder
     */
    public function __construct(
        Filesystem $filesystem,
        ProcessBuilder $processBuilder,
        Finder $finder
    ) {
        $this->fileSystem     = $filesystem;
        $this->processBuilder = $processBuilder;
        $this->finder         = $finder;
    }

    /**
     * @return WebcamVO[]
     */
    public function getPhotos()
    {
        $directory = ROOT . self::ROOT;
        if (!$this->fileSystem->exists($directory)) {
            $this->fileSystem->mkdir($directory, 0777);
        }

        $this->finder
            ->files()
            ->in($directory)
            ->name('*.jpg')
            ->sortByName();

        $vos = [];
        foreach ($this->finder as $file) {
            /** @var SplFileInfo $file */
            $filePath = $file->getPath();
            $relativePathName = $file->getRelativePathname();

            $webcamVo = $vos[]   = new WebcamVO();
            $webcamVo->filePath  = $filePath;
            $webcamVo->name      = $relativePathName;
            $webcamVo->webcamId  = $file->getBasename();
            $webcamVo->webPath   = sprintf('%s%s', substr(self::ROOT, 4), $webcamVo->name);
            $webcamVo->timestamp = $file->getCTime();
        }

        return $vos;
    }

    /**
     * @param string $name
     */
    public function takePhoto($name)
    {
        $path = $this->getFilename($name);

        $process = $this->processBuilder
            ->setArguments([self::EXECUTABLE, '-d', '/dev/video0', $path])
            ->setTimeout(self::TIMEOUT)
            ->getProcess();

        $process->run();

        $event = new WebcamEvent($name, WebcamEvent::TOOK_PHOTO);
        $this->dispatchEvent($event);
    }

    /**
     * @param string $shotId
     */
    public function delete($shotId)
    {
        $filename = $this->getFilename($shotId);

        $this->fileSystem->remove($filename);
    }

    /**
     * @param string $shotId
     * @return string
     */
    public function getFilename($shotId)
    {
        return sprintf('%s%s%s.%s', ROOT, self::ROOT, $shotId, self::EXTENSION);
    }
}
