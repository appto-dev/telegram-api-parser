<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/build')
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
    ])
    ->setFinder($finder);
