<?php
/** @var \CodeDocs\Config $config */

$config->baseDir = __DIR__;

$config->buildDir  = './build';
$config->docsDir   = './docs';
$config->classDirs = ['./src'];

$config->functions = [
    'execCommand' => function ($command) {

        $command = stripslashes($command);
        exec($command, $return);

        echo $command;
        echo implode("\n", $return);

        return $return;
    },
];
