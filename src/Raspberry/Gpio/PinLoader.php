<?php

namespace Raspberry\Gpio;

use Raspberry\Client\ClientInterface;

/**
 * @service(public=false)
 */
class PinLoader
{

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var PinsCollection
     */
    private $pins = null;

    /**
     * @Inject("@RaspberryClient")
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $pin
     * @return Pin
     */
    public function loadPin($pin)
    {
        $pins = $this->loadPins();

        return $pins->get($pin);
    }

    /**
     * @return PinsCollection
     */
    public function loadPins()
    {
        if (null !== $this->pins) {
            return $this->pins;
        }

        $results = $this->client->executeWithReturn(GpioManager::GPIO_COMMAND_READALL);
        $results = explode("\n", $results);
        $results = array_slice($results, 3, -2);

        $this->pins = new PinsCollection();
        foreach ($results as $r) {
            $matches = explode('|', $r);
            $matches = array_map('trim', $matches);

            $pin = new Pin();
            $pin->setID((int)$matches[1]);
            $pin->setName($matches[4]);
            $pin->setDirection($matches[5]);
            $pin->setValue((int)('High' === $matches[6]));

            $this->pins->add($pin);
        }

        return $this->pins;
    }
}
