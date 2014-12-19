<?php

namespace Raspberry\Gpio;

use Raspberry\Client\ClientInterface;

/**
 * @Service(public=false)
 */
class GpioManager
{

    const GPIO_COMMAND_READALL = 'gpio readall';
    const GPIO_COMMAND_DIRECTION = 'gpio mode %d %s';
    const GPIO_COMMAND_VALUE = 'gpio write %d %d';

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
     * @Inject({"@PinGateway", "@RaspberryClient", "@PinLoader"})
     * @param PinGateway $pin_gateway
     * @param ClientInterface $local_client
     * @param PinLoader $pinLoader
     */
    public function __construct(PinGateway $pin_gateway, ClientInterface $local_client, PinLoader $pinLoader)
    {
        $this->gateway  = $pin_gateway;
        $this->client = $local_client;
        $this->loader   = $pinLoader;
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
            if (!empty($descriptions[$pin->getId()])) {
                $pin->setDescription($descriptions[$pin->getId()]);
            }
        }

        return $pins;
    }

    /**
     * @param integer $id
     * @param string $status
     * @param boolean $value
     * @return Pin
     */
    public function setPin($id, $status, $value)
    {
        $pin = $this->loader->loadPin($id);

        $pin->setDirection($status ? Pin::DIRECTION_OUT : Pin::DIRECTION_IN);
        $pin->setValue($value ? Pin::VALUE_HIGH : Pin::VALUE_LOW);

        $this->updatePin($pin);

        return $pin;
    }

    /**
     * @param Pin $pin Pin
     */
    private function updatePin(Pin $pin)
    {
        $pinValue = Pin::VALUE_HIGH == $pin->getValue() ? 1 : 0;

        $this->client->execute(sprintf(self::GPIO_COMMAND_DIRECTION, $pin->getID(), $pin->getDirection()));
        $this->client->execute(sprintf(self::GPIO_COMMAND_VALUE, $pin->getID(), $pinValue));
    }
}
