<?php

namespace Homie\Espeak;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\TimeTrait;
use BrainExe\MessageQueue\Gateway;
use BrainExe\MessageQueue\Job;
use Homie\Client\ClientInterface;

/**
 * @Service(public=false)
 */
class Espeak
{

    use TimeTrait;

    const DEFAULT_SPEAKER = 'de+m1';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @Inject({"@MessageQueue.Gateway", "@HomieClient"})
     * @param Gateway $gateway
     * @param ClientInterface $client
     */
    public function __construct(Gateway $gateway, ClientInterface $client)
    {
        $this->client  = $client;
        $this->gateway = $gateway;
    }

    /**
     * @return array
     */
    public function getSpeakers()
    {
        return [
            'de+m1' => 'DE Male',
            'de+f1' => 'DE Female',
            'en'    => 'EN',
            'fr'    => 'FR'
        ];
    }

    /**
     * @return Job[]
     */
    public function getPendingJobs()
    {
        $now = $this->now();

        return $this->gateway->getEventsByType(EspeakEvent::SPEAK, $now);
    }

    /**
     * @param string $text
     * @param integer $volume
     * @param integer $speed
     * @param string $speaker
     */
    public function speak(
        $text,
        $volume,
        $speed,
        $speaker
    ) {
        if (empty($text)) {
            return;
        }

        $command = sprintf(
            'espeak "%s" -s "%d" -a "%d" -v%ss --stdout | aplay',
            $text,
            $speed,
            $volume,
            $speaker
        );

        $this->client->execute($command);
    }

    /**
     * @param string $jobId
     */
    public function deleteJob($jobId)
    {
        $this->gateway->deleteEvent($jobId, EspeakEvent::SPEAK);
    }
}
