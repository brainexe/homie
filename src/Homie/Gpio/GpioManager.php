<?php

namespace Homie\Gpio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Homie\Client\ClientInterface;

/**
 * @Service(public=false)
 */
class GpioManager
{

    // todo put into config.xml
    const GPIO_COMMAND_READALL   = 'gpio readall';
    const GPIO_COMMAND_DIRECTION = 'gpio mode %d %s';
    const GPIO_COMMAND_VALUE     = 'gpio write %d %d';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var PinGateway
     */
    private $gateway;

    /**
     * @var PinLoader
     */
    private $loader;

    /**
     * @Inject({"@PinGateway", "@HomieClient", "@PinLoader"})
     * @param PinGateway $gateway
     * @param ClientInterface $client
     * @param PinLoader $loader
     */
    public function __construct(
        PinGateway $gateway,
        ClientInterface $client,
        PinLoader $loader
    ) {
        $this->gateway = $gateway;
        $this->client  = $client;
        $this->loader  = $loader;
    }

    /**
     * @return PinsCollection
     */
    public function getPins()
    {
        $descriptions = $this->gateway->getPinDescriptions();

        $pins = $this->loader->loadPins();

        foreach ($pins->getAll() as $pin) {
            /** @var Pin $pin */
            if (!empty($descriptions[$pin->getWiringId()])) {
                $pin->setDescription($descriptions[$pin->getWiringId()]);
            }
        }

        return $pins;
    }

    /**
     * @param integer $pinId
     * @param string $status
     * @param boolean $value
     * @return Pin
     */
    public function setPin($pinId, $status, $value)
    {
        $pin = $this->loader->loadPin($pinId);

        $pin->setMode($status ? Pin::DIRECTION_OUT : Pin::DIRECTION_IN);
        $pin->setValue($value ? 1 : 0);

        $this->updatePin($pin);

        return $pin;
    }

    /**
     * @param int $pinId
     * @param string $description
     */
    public function setDescription($pinId, $description)
    {
        $this->gateway->setDescription($pinId, $description);
    }

    /**
     * @param Pin $pin Pin
     */
    private function updatePin(Pin $pin)
    {
        $pinValue = (bool)$pin->getValue();

        $command = sprintf(
            self::GPIO_COMMAND_DIRECTION,
            $pin->getWiringId(),
            escapeshellarg($pin->getMode())
        );
        $this->client->execute($command);

        $command = sprintf(
            self::GPIO_COMMAND_VALUE,
            $pin->getWiringId(),
            $pinValue
        );
        $this->client->execute($command);
    }
}
