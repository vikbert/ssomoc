<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude([
        'assets',
        'bin',
        'features',
        'node_modules',
        'var',
        'vendor',
        'web',
    ])
    ->in(__DIR__);

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@Symfony' => true,
    'yoda_style' => [
        'equal' => null,
        'identical' => null,
        'less_and_greater' => null,
    ],
    'concat_space' => ['spacing' => 'one'],
    'declare_equal_normalize' => ['space' => 'single'],
    'array_syntax' => ['syntax' => 'short'],
    'array_indentation' => true,
    'phpdoc_align' => [
        'align' => 'left',
        'tags' => ['param', 'property', 'return', 'throws', 'type', 'var', 'method'],
    ],
    'phpdoc_to_comment' => false,
    'no_superfluous_phpdoc_tags' => true,
])
    ->setFinder($finder);
