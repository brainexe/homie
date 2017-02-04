<?php

namespace Homie\IFTTT;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;

/**
 * @Service("IFTTT.Trigger")
 */
class Trigger
{

    const BASE_URL = 'https://maker.ifttt.com/trigger/%s/with/key/%s';

    /**
     * @var string
     */
    private $key;

    /**
     * @Inject({"%ifttt.key%"})
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * @param string $eventName
     * @return string
     */
    public function trigger(string $eventName)
    {
        $url = sprintf(self::BASE_URL, $eventName, $this->key);

        $context = stream_context_create(['http' => ['method' => 'POST']]);

        return file_get_contents($url, false, $context);
    }
}
