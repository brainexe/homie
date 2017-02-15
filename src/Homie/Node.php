<?php

namespace Homie;

use BrainExe\Core\Traits\JsonSerializableTrait;
use BrainExe\Core\Translation\TranslationProvider;
use InvalidArgumentException;
use JsonSerializable;

class Node implements JsonSerializable, TranslationProvider
{
    use JsonSerializableTrait;

    const TYPE_RASPBERRY = 'raspberry';
    const TYPE_ARDUINO   = 'arduino';
    const TYPE_METAWEAR  = 'metawear';
    const TYPE_SERVER    = 'server';
    const TYPE_PARTICLE  = 'particle';
    const TYPE_DISPLAY   = 'display';
    const TYPE_OPEN_HAB  = 'openHab';

    const TYPES = [
        self::TYPE_RASPBERRY,
        self::TYPE_ARDUINO,
        self::TYPE_METAWEAR,
        self::TYPE_SERVER,
        self::TYPE_PARTICLE,
        self::TYPE_DISPLAY,
        self::TYPE_OPEN_HAB,
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
     * @var array
     */
    private $options;

    /**
     * @param int $nodeId
     * @param string $type
     * @param string $name
     * @param array $options
     */
    public function __construct(
        int $nodeId,
        string $type,
        string $name = '',
        array $options = []
    ) {
        $this->nodeId  = $nodeId;
        $this->type    = $type;
        $this->options = $options;
        $this->name    = $name;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getNodeId() : int
    {
        return $this->nodeId;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * @param string $key
     * @return string
     */
    public function getOption(string $key) : string
    {
        if (!array_key_exists($key, $this->options)) {
            throw new InvalidArgumentException(sprintf('Invalid option: %s', $key));
        }

        return $this->options[$key];
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
