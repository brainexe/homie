<?php

namespace Homie\Radio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\UserException;
use Exception;
use Generator;
use Homie\Radio\VO\GpioSwitchVO;
use Homie\Radio\VO\SwitchVO;
use InvalidArgumentException;
use Homie\Radio\VO\RadioVO;

/**
 * @Service("Switch.Switches", public=false)
 */
class Switches
{

    const RADIO_PINS = [
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
     * @Inject("@Switch.Gateway")
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
            $flipped = array_flip(self::RADIO_PINS);
            if (!isset($flipped[$pin])) {
                throw new UserException(sprintf("Invalid pin: %s", $pin));
            }
            return $pin;
        }

        $pin = strtoupper($pin);
        if (empty(self::RADIO_PINS[$pin])) {
            throw new UserException(sprintf("Invalid pin: %s", $pin));
        }

        return self::RADIO_PINS[$pin];
    }

    /**
     * @param integer $switchId
     * @return SwitchVO
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
     * @return Generator|SwitchVO[]
     */
    public function getAll()
    {
        $raw = $this->gateway->getAll();

        foreach ($raw as $switchRaw) {
            yield $switchRaw['switchId'] => $this->buildSwitchVO($switchRaw);
        }
    }

    /**
     * @param SwitchVO $switchVO
     * @return integer new switch id
     */
    public function add(SwitchVO $switchVO)
    {
        return $this->gateway->add($switchVO);
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
     * @return SwitchVO
     * @throws Exception
     */
    private function buildSwitchVO(array $raw)
    {
        $type = $raw['type'];
        switch ($type) {
            case RadioVO::TYPE:
                $switch = new RadioVO();
                $switch->code = $raw['code'];
                $switch->pin  = $raw['pin'];
                break;
            case GpioSwitchVO::TYPE:
                $switch = new GpioSwitchVO();
                $switch->pin = $raw['pin'];
                break;
            default:
                throw new Exception(sprintf('Invalid switch type: %s', $type));
        }

        $switch->switchId    = $raw['switchId'];
        $switch->name        = $raw['name'];
        $switch->description = $raw['description'];

        return $switch;
    }
}
