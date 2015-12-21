<?php

namespace Homie;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\SerializableTrait;
use BrainExe\Core\Translation\TranslationProvider;
use JsonSerializable;

/**
 * @Service(public=false)
 */
class Node implements JsonSerializable, TranslationProvider
{
    use SerializableTrait;

    const TYPE_RASPBERRY = 'raspberry';
    const TYPE_ARDUINO   = 'arduino';
    const TYPE_METAWEAR  = 'metawear';
    const TYPE_SERVER    = 'server';

    const TYPES = [
        self::TYPE_RASPBERRY,
        self::TYPE_ARDUINO,
        self::TYPE_METAWEAR,
        self::TYPE_SERVER,
    ];

    /**
     * @var int
     */
    private $nodeId;

    /**
     * @see self::TYPE_*
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * e.g. IP or GPIO-PIN
     * @var string
     */
    private $address;

    /**
     * @param int $nodeId
     * @param string $type
     * @param string $name
     * @param string $address
     */
    public function __construct($nodeId, $type, $name, $address)
    {
        $this->nodeId  = $nodeId;
        $this->type    = $type;
        $this->address = $address;
        $this->name    = $name;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * @return string[]
     */
    public static function getTokens()
    {
        foreach (self::TYPES as $type) {
            yield sprintf('node.%s.name', $type);
        }
    }
}
