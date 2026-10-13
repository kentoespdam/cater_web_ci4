<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/tests',
    ])
    ->exclude('ThirdParty');

$config = new PhpCsFixer\Config();
return $config
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters']],
        'phpdoc_align' => ['align' => 'vertical'],
        'cast_spaces' => ['space' => 'single'],
        'blank_line_before_statement' => [
            'statements' => ['return', 'try', 'if', 'switch', 'while', 'for', 'foreach'],
        ],
    ])
    ->setFinder($finder)
    ->setUsingCache(true);
