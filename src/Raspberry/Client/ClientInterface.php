<?php

namespace Raspberry\Client;

interface ClientInterface
{

    /**
     * @param string $command
     */
    public function execute($command);

    /**
     * @todo rename to executeWithResult etc.
     * @param string $command
     * @return string
     */
    public function executeWithReturn($command);
}
