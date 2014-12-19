<?php

namespace Raspberry\Webcam;

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

    const ROOT = 'web/static/webcam/';
    const EXTENSION = 'jpg';
    const TIMEOUT = 10000;
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
     * @inject({"@Filesystem", "@ProcessBuilder", "@Finder"})
     * @param Filesystem $filesystem
     * @param ProcessBuilder $processBuilder
     * @param Finder $finder
     */
    public function __construct(Filesystem $filesystem, ProcessBuilder $processBuilder, Finder $finder)
    {
        $this->fileSystem = $filesystem;
        $this->processBuilder = $processBuilder;
        $this->finder = $finder;
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

        $webcam_vos = [];
        foreach ($this->finder as $file) {
            /** @var SplFileInfo $file */
            $file_path = $file->getPath();
            $relative_path_name = $file->getRelativePathname();

            $webcam_vo = $webcam_vos[] = new WebcamVO();
            $webcam_vo->file_path = $file_path;
            $webcam_vo->name = $relative_path_name;
            $webcam_vo->id = $file->getBasename();
            $webcam_vo->web_path = sprintf('%s%s', substr(self::ROOT, 4), $webcam_vo->name);
            $webcam_vo->timestamp = $file->getCTime();
        }

        return $webcam_vos;
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
     * @param string $id
     */
    public function delete($id)
    {
        $filename = $this->getFilename($id);

        $this->fileSystem->remove($filename);
    }

    /**
     * @param string $id
     * @return string
     */
    public function getFilename($id)
    {
        return sprintf('%s%s%s.%s', ROOT, self::ROOT, $id, self::EXTENSION);
    }
}
