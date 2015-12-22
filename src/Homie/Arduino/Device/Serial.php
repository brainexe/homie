<?php

namespace Homie\Arduino\Device;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Util\Glob;
use Homie\Arduino\Device;
use Homie\Arduino\SerialEvent;
use Homie\Client\ClientInterface;
use RuntimeException;

/**
 * @Service("Arduino.Device.Serial", public=false)
 */
class Serial implements Device
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
     * @var Glob
     */
    private $glob;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @Inject({"@Glob", "@HomieClient", "%serial.port%", "%serial.baud%"})
     * @param Glob $glob
     * @param ClientInterface $client
     * @param string $serialPort
     * @param int $serialBaud
     */
    public function __construct(
        Glob $glob,
        ClientInterface $client,
        $serialPort,
        $serialBaud
    ) {
        $this->serialBaud     = $serialBaud;
        $this->client         = $client;
        $this->serialPort     = $serialPort;
        $this->glob           = $glob;
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
        $files = $this->glob->execGlob($this->serialPort);

        if (empty($files)) {
            throw new RuntimeException(
                sprintf("No file found matching %s", $this->serialPort)
            );
        }

        $filename = current($files);

        $command = sprintf(
            'sudo stty -F %s %d',
            escapeshellarg($filename),
            $this->serialBaud
        );
        $this->client->execute($command);

        $this->fileHandle = fopen($filename, 'w+');
    }
}
