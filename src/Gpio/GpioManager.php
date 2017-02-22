<?php

namespace Homie\Gpio;


use BrainExe\Core\Annotations\Service;
use Homie\Gpio\Adapter\Factory;
use Homie\Node;

/**
 * @Service("GPIO.GpioManager")
 */
class GpioManager
{
    /**
     * @var PinGateway
     */
    private $gateway;

    /**
     * @var Factory
     */
    private $adapterFactory;

    /**
     * @param PinGateway $gateway
     * @param Factory $adapterFactory
     */
    public function __construct(
        PinGateway $gateway,
        Factory    $adapterFactory
    ) {
        $this->gateway        = $gateway;
        $this->adapterFactory = $adapterFactory;
    }

    /**
     * @param Node $node
     * @return PinsCollection
     */
    public function getPins(Node $node) : PinsCollection
    {
        $descriptions = $this->gateway->getPinDescriptions();

        $adapter = $this->adapterFactory->getForNode($node);

        $pins = $adapter->loadPins();

        foreach ($pins->getAll() as $pin) {
            /** @var Pin $pin */
            if (!empty($descriptions[$pin->getPhysicalId()])) {
                $pin->setDescription($descriptions[$pin->getPhysicalId()]);
            }
        }

        return $pins;
    }

    /**
     * @param Node $node
     * @param int $pinId
     * @param bool $status
     * @param bool $value
     * @return Pin
     */
    public function setPin(Node $node, int $pinId, bool $status, bool $value) : Pin
    {
        $adapter = $this->adapterFactory->getForNode($node);

        $pin = $adapter->loadPin($pinId);

        $pin->setMode($status ? Pin::DIRECTION_OUT : Pin::DIRECTION_IN);
        $pin->setValue($value ? 1 : 0);

        $this->updatePin($node, $pin);

        return $pin;
    }

    /**
     * @param Node $node
     * @param int $pinId
     * @param string $description
     */
    public function setDescription(Node $node, int $pinId, string $description)
    {
        $this->gateway->setDescription($pinId, $description);
    }

    /**
     * @param Node $node
     * @param Pin $pin Pin
     */
    private function updatePin(Node $node, Pin $pin)
    {
        $adapter = $this->adapterFactory->getForNode($node);

        $adapter->updatePin($pin);
    }
}
