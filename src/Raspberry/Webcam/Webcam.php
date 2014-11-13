<?php

namespace Raspberry\Webcam;

use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Service(public=false)
 */
class Webcam {

	const ROOT = 'web/static/webcam/';
	const EXTENSION = 'jpg';
	const TIMEOUT = 10000;
	const EXECUTABLE = 'fswebcam';

	use EventDispatcherTrait;

	/**
	 * @var Filesystem
	 */
	private $_fileSystem;

	/**
	 * @var ProcessBuilder
	 */
	private $_processBuilder;

	/**
	 * @var Finder
	 */
	private $_finder;

	/**
	 * @inject({"@Filesystem", "@ProcessBuilder", "@Finder"})
	 * @param Filesystem $filesystem
	 * @param ProcessBuilder $processBuilder
	 * @param Finder $finder
	 */
	public function __construct(Filesystem $filesystem, ProcessBuilder $processBuilder, Finder $finder) {
		$this->_fileSystem = $filesystem;
		$this->_processBuilder = $processBuilder;
		$this->_finder = $finder;
	}

	/**
	 * @return WebcamVO[]
	 */
	public function getPhotos() {
		$directory = ROOT . self::ROOT;
		if (!$this->_fileSystem->exists($directory)) {
			$this->_fileSystem->mkdir($directory, 0777);
		}

		$this->_finder
			->files()
			->in($directory)
			->name('*.jpg')
			->sortByName();

		$webcam_vos = [];
		foreach ($this->_finder as $file) {
			/** @var SplFileInfo $file */
			$webcam_vo = $webcam_vos[] = new WebcamVO();
			$webcam_vo->file_path = $file->getPath();
			$webcam_vo->id = basename($file->getRelativePathname());
			$webcam_vo->name = $file->getRelativePathname();
			$webcam_vo->web_path = sprintf('%s%s', substr(self::ROOT, 4), $webcam_vo->name);
			$webcam_vo->timestamp = filectime($file->getPath());
		}

		return $webcam_vos;
	}

	/**
	 * @param string $name
	 */
	public function takePhoto($name) {
		$path = $this->getFilename($name);

		$process = $this->_processBuilder
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
	public function delete($id) {
		$filename = $this->getFilename($id);

		$this->_fileSystem->remove($filename);
	}

	/**
	 * @param string $id
	 * @return string
	 */
	public function getFilename($id) {
		return sprintf('%s%s%s.%s', ROOT, self::ROOT, $id, self::EXTENSION);
	}
}
