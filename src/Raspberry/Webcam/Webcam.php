<?php

namespace Raspberry\Webcam;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

/**
 * @DI\Service(public=false)
 */
class Webcam {
	const ROOT = '/htdocs/webcam/';

	/**
	 * @return SplFileInfo
	 */
	public function getPhotos() {
		$finder = new Finder();
		return $finder->in(ROOT . self::ROOT)->files()->sortByName();
	}

	/**
	 * @param string $path
	 */
	public function takePhoto($path) {
		$command = sprintf('fswebcam -d /dev/video0 %s', $path);

		$process = new Process($command);
		$process->setTimeout(10000);
		$process->run();

	}
} 