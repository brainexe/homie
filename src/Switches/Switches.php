<?php

namespace Homie\Switches;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Application\UserException;
use Exception;
use Generator;
use Homie\Switches\VO\ArduinoSwitchVO;
use Homie\Switches\VO\GpioSwitchVO;
use Homie\Switches\VO\ParticleVO;
use Homie\Switches\VO\SwitchVO;
use InvalidArgumentException;
use Homie\Switches\VO\RadioVO;

/**
 * @Service
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
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param int|string $pin
     * @throws UserException
     * @return int
     */
    public function getRadioPin($pin) : int
    {
        if (is_numeric($pin)) {
            $pin     = (int)$pin;
            $flipped = array_flip(self::RADIO_PINS);
            if (!isset($flipped[$pin])) {
                throw new UserException(sprintf('Invalid pin: %s', $pin));
            }
            return $pin;
        }

        $pin = strtoupper($pin);
        if (empty(self::RADIO_PINS[$pin])) {
            throw new UserException(sprintf('Invalid pin: %s', $pin));
        }

        return self::RADIO_PINS[$pin];
    }

    /**
     * @param int $switchId
     * @return SwitchVO
     * @throws InvalidArgumentException
     */
    public function get(int $switchId) : SwitchVO
    {
        $raw = $this->gateway->get($switchId);

        if (empty($raw)) {
            throw new InvalidArgumentException(
                sprintf('Invalid switch: %d', $switchId)
            );
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
    public function add(SwitchVO $switchVO) : int
    {
        return $this->gateway->add($switchVO);
    }

    /**
     * @param int $switchId
     */
    public function delete(int $switchId)
    {
        $this->gateway->delete($switchId);
    }

    /**
     * @param array $raw
     * @return SwitchVO
     * @throws Exception
     */
    private function buildSwitchVO(array $raw) : SwitchVO
    {
        $switch = $this->buildBaseVo($raw);
        $switch->switchId    = $raw['switchId'];
        $switch->name        = $raw['name'];
        $switch->description = $raw['description'];
        $switch->status      = $raw['status'];

        return $switch;
    }

    /**
     * @param array $raw
     * @return SwitchVO
     * @throws Exception
     */
    private function buildBaseVo(array $raw) : SwitchVO
    {
        switch ($raw['type']) {
            case RadioVO::TYPE:
                $switch       = new RadioVO();
                $switch->code = $raw['code'];
                $switch->pin  = $raw['pin'];
                break;
            case GpioSwitchVO::TYPE:
                $switch      = new GpioSwitchVO();
                $switch->pin = $raw['pin'];
                break;
            case ArduinoSwitchVO::TYPE:
                $switch         = new ArduinoSwitchVO();
                $switch->pin    = $raw['pin'];
                $switch->nodeId = $raw['nodeId'];
                break;
            case ParticleVO::TYPE:
                $switch           = new ParticleVO();
                $switch->function = $raw['function'];
                $switch->nodeId   = $raw['nodeId'];
                break;
            default:
                throw new InvalidArgumentException(
                    sprintf('Invalid switch type: %s', $raw['type'])
                );
        }

        return $switch;
    }
}
