<?php

namespace Homie\Client;

interface ClientInterface
{

    /**
     * @param string $command
     * @param string[] $arguments
     */
    public function execute(string $command, array $arguments = []);

    /**
     * @param string $command
     * @param string[] $arguments
     * @return string
     */
    public function executeWithReturn(string $command, array $arguments = []) : string;
}
