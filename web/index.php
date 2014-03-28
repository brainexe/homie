<?php

/** @var Container $dic */
use Symfony\Component\DependencyInjection\Container;

$dic = include __DIR__ . '/../src/bootstrap.php';

include  __DIR__ . '/../vendor/matze/core/scripts/web.php';
