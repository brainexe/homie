<?php

namespace Homie\IFTTT;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;

/**
 * @Service("IFTTT.Action", public=false)
 */
class Action
{

    const BASE_URL = 'https://maker.ifttt.com/trigger/%s/with/key/%s';

    /**
     * @var string
     */
    private $key;

    /**
     * @Inject({"%ifttt.key%"})
     * @param $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * @param string $eventName
     * @return string
     */
    public function trigger($eventName)
    {
        $url = sprintf(self::BASE_URL, $eventName, $this->key);

        $options = array(
            'http' => array(
                'method'  => 'POST',
            ),
        );
        $context  = stream_context_create($options);

        return file_get_contents($url, false, $context);
    }
}
