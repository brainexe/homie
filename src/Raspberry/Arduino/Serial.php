<?php

namespace Raspberry\Arduino;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @Service("Arduino.Serial", public=false)
 */
class Serial
{

    /**
     * @var string
     */
    private $serialPort;

    /**
     * @var integer
     */
    private $serialBaud;

    /**
     * @var Resource
     */
    private $fileHandle;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @Inject({"@Finder", "%serial.port%", "%serial.baud%"})
     * @param Finder $finder
     * @param string $serialPort
     * @param int $serialBaud
     */
    public function __construct(Finder $finder, $serialPort, $serialBaud)
    {
        $this->serialBaud = $serialBaud;
        $this->serialPort = $serialPort;
        $this->finder     = $finder;
    }

    /**
     * @param SerialEvent $event
     */
    public function sendSerial(SerialEvent $event)
    {
        if (!$this->fileHandle) {
            $this->initSerial();
        }

        $line = sprintf(
            "%s:%d:%d\n",
            $event->getAction(),
            $event->getPin(),
            $event->getValue()
        );

        fwrite($this->fileHandle, $line);
    }

    private function initSerial()
    {
        $iterator = $this
            ->finder
            ->in('/dev')
            ->name($this->serialPort)
            ->getIterator();

        /** @var SplFileInfo $file */
        $file =  $iterator->current();

        if ($file === null) {
            throw new RuntimeException(
                sprintf("no file found matching %s", $this->serialPort)
            );
        }

        $filename = $file->getPathname();
        exec(sprintf('sudo stty -F %s %d', $filename, $this->serialBaud));

        $this->fileHandle = fopen($filename, 'w+');
    }
}
