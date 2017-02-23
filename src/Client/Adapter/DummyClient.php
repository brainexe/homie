<?php

namespace Homie\Client\Adapter;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Traits\LoggerTrait;
use Homie\Client\ClientInterface;

/**
 * @Service("HomieClient.Dummy")
 */
class DummyClient implements ClientInterface
{

    use LoggerTrait;

    /**
     * {@inheritdoc}
     */
    public function execute(string $command, array $arguments = []) : void
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
