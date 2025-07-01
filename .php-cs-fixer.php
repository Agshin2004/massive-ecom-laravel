<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS' => true,
        '@PHP82Migration' => true,
        'no_unused_imports' => false,
    ])
    ->setFinder($finder);
