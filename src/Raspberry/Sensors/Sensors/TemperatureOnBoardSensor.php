<?php

namespace Raspberry\Sensors\Sensors;

use BrainExe\Core\Util\FileSystem;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Service(public=false, tags={{"name" = "sensor"}})
 */
class TemperatureOnBoardSensor implements SensorInterface {

	const PATH = '/sys/class/thermal/thermal_zone0/temp';
	const TYPE = 'temperature_onboard';

	use TemperatureSensorTrait;

	/**
	 * @var Filesystem
	 */
	private $fileSystem;

	/**
	 * @inject("@FileSystem")
	 * @param Filesystem $filesystem
	 */
	public function __construct(Filesystem $filesystem) {
		$this->fileSystem = $filesystem;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSensorType() {
		return self::TYPE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValue($pin) {
		$content = $this->fileSystem->fileGetContents(self::PATH);

		return $content / 1000;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isSupported(OutputInterface $output) {
		if (!$this->fileSystem->exists(self::PATH)) {
			$output->writeln(sprintf('<error>%s: Thermal zone file does not exist: %s</error>', self::getSensorType(), self::PATH));
			return false;
		}

		return true;
	}

}
