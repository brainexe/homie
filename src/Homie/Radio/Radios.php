<?php

namespace Homie\Radio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\UserException;
use InvalidArgumentException;
use Homie\Radio\VO\RadioVO;

/**
 * @Service(public=false)
 */
class Radios
{

    /**
     * @var array
     */
    public static $radioPins = [
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
     * @param RadioGateway $gateway
     */
    public function __construct(RadioGateway $gateway)
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
            $flipped = array_flip(self::$radioPins);
            if (!isset($flipped[$pin])) {
                throw new UserException(sprintf("Invalid pin: %s", $pin));
            }
            return $pin;
        }

        $pin = strtoupper($pin);
        if (empty(self::$radioPins[$pin])) {
            throw new UserException(sprintf("Invalid pin: %s", $pin));
        }

        return self::$radioPins[$pin];
    }

    /**
     * @param integer $radioId
     * @return RadioVO
     */
    public function getRadio($radioId)
    {
        $raw = $this->gateway->getRadio($radioId);

        if (empty($raw)) {
            throw new InvalidArgumentException(sprintf('Invalid radio: %d', $radioId));
        }

        return $this->buildRadioVO($raw);
    }

    /**
     * @return RadioVO[]
     */
    public function getRadios()
    {
        $radios = [];
        $radiosRaw = $this->gateway->getRadios();

        foreach ($radiosRaw as $radio) {
            $radios[$radio['radioId']] = $this->buildRadioVO($radio);
        }

        return $radios;
    }

    /**
     * @param RadioVO $radioVo
     * @return integer $radioId
     */
    public function addRadio(RadioVO $radioVo)
    {
        return $this->gateway->addRadio($radioVo);
    }

    /**
     * @param integer $radioId
     */
    public function deleteRadio($radioId)
    {
        $this->gateway->deleteRadio($radioId);
    }

    /**
     * @param array $raw
     * @return RadioVO
     */
    private function buildRadioVO(array $raw)
    {
        $radioVo              = new RadioVO();
        $radioVo->radioId     = $raw['radioId'];
        $radioVo->name        = $raw['name'];
        $radioVo->description = $raw['description'];
        $radioVo->code        = $raw['code'];
        $radioVo->pin         = $raw['pin'];

        return $radioVo;
    }
}
