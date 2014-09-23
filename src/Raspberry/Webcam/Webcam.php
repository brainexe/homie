<?php

namespace Raspberry\Webcam;

use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

/**
 * @Service
 */
class Webcam {
	const ROOT = 'web/static/webcam/';
	const EXTENSION = 'jpg';

	use EventDispatcherTrait;

	/**
	 * @return WebcamVO[]
	 */
	public function getPhotos() {
		$directory = ROOT . self::ROOT;
		if (!is_dir($directory)) {
			mkdir($directory, 0777, true);
		}

		$finder = new Finder();
		$finder
			->files()
			->in($directory)
			->name('*.jpg')
			->sortByName();

		$webcam_vos = [];
		foreach ($finder as $file) {
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
		$command = sprintf('fswebcam -d /dev/video0 %s', $path);

		$process = new Process($command);
		$process->setTimeout(10000);
		$process->run();

		$event = new WebcamEvent($name, WebcamEvent::TOOK_PHOTO);
		$this->dispatchEvent($event);
	}

	/**
	 * @param string $id
	 */
	public function delete($id) {
		$filename = $this->getFilename($id);

		$filesystem = new Filesystem();
		$filesystem->remove($filename);
	}

	/**
	 * @param string $id
	 * @return string
	 */
	public function getFilename($id) {
		return sprintf('%s%s%s.%s', ROOT, self::ROOT, $id, self::EXTENSION);
	}
}
