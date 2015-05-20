<?php

namespace Homie\Client;

interface ClientInterface
{

    /**
     * @param string $command
     * @param string[] $arguments
     */
    public function execute($command, array $arguments = []);

    /**
     * @todo rename to executeWithResult etc.
     * @param string $command
     * @param string[] $arguments
     * @return string
     */
    public function executeWithReturn($command, array $arguments = []);
}
