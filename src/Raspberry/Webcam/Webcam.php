<?php

namespace Raspberry\Webcam;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

/**
 * @Service
 */
class Webcam {
	const ROOT = '/web/static/webcam/';
	const EXTENSION = 'jpg';

	/**
	 * @return WebcamVO[]
	 */
	public function getPhotos() {
		$finder = new Finder();
		$finder
			->files()
			->in(ROOT . self::ROOT)
			->name('*.jpg')
			->sortByName();

		$webcam_vos = [];
		foreach ($finder as $file) {
			/** @var SplFileInfo $file */
			$webcam_vo = $webcam_vos[] = new WebcamVO();
			$webcam_vo->file_path = $file->getPath();
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
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public function getFilename($name) {
		return sprintf('%s%s%s.%s', ROOT, self::ROOT, $name, self::EXTENSION);
	}

	/**
	 * @param string $shot_id
	 */
	public function delete($shot_id) {
		// TODO
	}
}
