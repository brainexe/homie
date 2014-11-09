<?php

namespace Raspberry\Media;
use Symfony\Component\Process\Process;

/**
 * @Service(public=false)
 */
class Sound {

	/**
	 * @param string $file
	 */
	public function playSound($file) {
		$process = new Process(sprintf('mplayer %s', $file));
		$process->run();
	}
} 