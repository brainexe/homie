<?php

namespace Raspberry\Radio;

use BrainExe\Core\Application\UserException;
use InvalidArgumentException;
use Raspberry\Radio\VO\RadioVO;

/**
 * @Service(public=false)
 */
class Radios
{

    /**
     * @var array
     */
    public static $radio_pins = [
    'A' => 1,
    'B' => 2,
    'C' => 3,
    'D' => 4,
    'E' => 5,
    ];

    /**
     * @var RadioGateway
     */
    private $gateway;

    /**
     * @Inject("@RadioGateway")
     * @param RadioGateway $radio_gateway
     */
    public function __construct(RadioGateway $radio_gateway)
    {
        $this->gateway = $radio_gateway;
    }

    /**
     * @param integer|string $pin
     * @throws UserException
     * @return integer
     */
    public function getRadioPin($pin)
    {
        if (is_numeric($pin)) {
            $pin = (int)$pin;
            $flipped = array_flip(self::$radio_pins);
            if (!isset($flipped[$pin])) {
                throw new UserException(sprintf("Invalid pin: %s", $pin));
            }
            return $pin;
        }

        $pin = strtoupper($pin);
        if (empty(self::$radio_pins[$pin])) {
            throw new UserException(sprintf("Invalid pin: %s", $pin));
        }

        return self::$radio_pins[$pin];
    }

    /**
     * @param integer $radio_id
     * @return RadioVO
     */
    public function getRadio($radio_id)
    {
        $raw = $this->gateway->getRadio($radio_id);

        if (empty($raw)) {
            throw new InvalidArgumentException(sprintf('Invalid radio: %d', $radio_id));
        }

        return $this->buildRadioVO($raw);
    }

    /**
     * @return array[]
     */
    public function getRadios()
    {
        $radios = [];
        $radios_raw = $this->gateway->getRadios();

        foreach ($radios_raw as $radio) {
            $radios[$radio['id']] = $this->buildRadioVO($radio);
        }

        return $radios;
    }

    /**
     * @param RadioVO $radio_vo
     * @return integer $radio_id
     */
    public function addRadio(RadioVO $radio_vo)
    {
        return $this->gateway->addRadio($radio_vo);
    }

    /**
     * @param integer $radio_id
     */
    public function deleteRadio($radio_id)
    {
        $this->gateway->deleteRadio($radio_id);
    }

    /**
     * @param array $raw
     * @return RadioVO
     */
    private function buildRadioVO(array $raw)
    {
        $radio_vo = new RadioVO();
        $radio_vo->id = $raw['id'];
        $radio_vo->name = $raw['name'];
        $radio_vo->description = $raw['description'];
        $radio_vo->code = $raw['code'];
        $radio_vo->pin = $raw['pin'];

        return $radio_vo;
    }
}
