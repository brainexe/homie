<?php

namespace Homie\Radio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\UserException;
use Generator;
use InvalidArgumentException;
use Homie\Radio\VO\RadioVO;

/**
 * @Service(public=false)
 */
class Radios
{

    const PINS = [
        'A' => 1,
        'B' => 2,
        'C' => 3,
        'D' => 4,
        'E' => 5,
    ];

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @Inject("@RadioGateway")
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param integer|string $pin
     * @throws UserException
     * @return integer
     */
    public function getRadioPin($pin)
    {
        if (is_numeric($pin)) {
            $pin     = (int)$pin;
            $flipped = array_flip(self::PINS);
            if (!isset($flipped[$pin])) {
                throw new UserException(sprintf("Invalid pin: %s", $pin));
            }
            return $pin;
        }

        $pin = strtoupper($pin);
        if (empty(self::PINS[$pin])) {
            throw new UserException(sprintf("Invalid pin: %s", $pin));
        }

        return self::PINS[$pin];
    }

    /**
     * @param integer $switchId
     * @return RadioVO
     */
    public function get($switchId)
    {
        $raw = $this->gateway->get($switchId);

        if (empty($raw)) {
            throw new InvalidArgumentException(sprintf('Invalid switch: %d', $switchId));
        }

        return $this->buildSwitchVO($raw);
    }

    /**
     * @return Generator|RadioVO[]
     */
    public function getRadios()
    {
        $radiosRaw = $this->gateway->getAll();

        foreach ($radiosRaw as $radio) {
            yield $radio['switchId'] => $this->buildSwitchVO($radio);
        }
    }

    /**
     * @param RadioVO $radioVo
     * @return integer new switch id
     */
    public function addRadio(RadioVO $radioVo)
    {
        return $this->gateway->add($radioVo);
    }

    /**
     * @param integer $switchId
     */
    public function delete($switchId)
    {
        $this->gateway->delete($switchId);
    }

    /**
     * @param array $raw
     * @return RadioVO
     */
    private function buildSwitchVO(array $raw)
    {
        $radioVo              = new RadioVO();
        $radioVo->switchId    = $raw['switchId'];
        $radioVo->name        = $raw['name'];
        $radioVo->description = $raw['description'];
        $radioVo->code        = $raw['code'];
        $radioVo->pin         = $raw['pin'];

        return $radioVo;
    }
}
