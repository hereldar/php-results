<?php

$finder = PhpCsFixer\Finder
    ::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests');

$config = new PhpCsFixer\Config();
$config->setFinder($finder);
$config->setRules([
    '@PHP81Migration' => true,
    '@PSR12' => true,
]);

return $config;