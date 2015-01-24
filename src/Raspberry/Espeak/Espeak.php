<?php

namespace Raspberry\Espeak;

use BrainExe\Core\Traits\TimeTrait;
use BrainExe\MessageQueue\MessageQueueGateway;
use BrainExe\MessageQueue\MessageQueueJob;
use Raspberry\Client\ClientInterface;

/**
 * @Service(public=false)
 */
class Espeak implements SpeakOutputInterface
{

    use TimeTrait;

    const DEFAULT_SPEAKER = 'de+m1';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var MessageQueueGateway
     */
    private $gateway;

    /**
     * @Inject({"@MessageQueueGateway", "@RaspberryClient"})
     * @param MessageQueueGateway $gateway
     * @param ClientInterface $client
     */
    public function __construct(MessageQueueGateway $gateway, ClientInterface $client)
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
     * @return MessageQueueJob[]
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
    public function speak($text, $volume = 100, $speed = 100, $speaker = self::DEFAULT_SPEAKER)
    {
        if (empty($text)) {
            return;
        }

        $command = sprintf(
            'espeak "%s" -s %d -a %d  -v%ss --stdout | aplay',
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
