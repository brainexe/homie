<?php

namespace Homie\Client;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\LoggerTrait;

/**
 * @Service("HomieClient.Dummy", public=false)
 */
class DummyClient implements ClientInterface
{

    use LoggerTrait;

    /**
     * {@inheritdoc}
     */
    public function execute(string $command, array $arguments = [])
    {
        $this->info(sprintf('%s %s', $command, implode(' ', $arguments)));
    }

    /**
     * {@inheritdoc}
     */
    public function executeWithReturn(string $command, array $arguments = []) : string
    {
        $this->info(sprintf('%s %s', $command, implode(' ', $arguments)));

        return '';
    }
}
