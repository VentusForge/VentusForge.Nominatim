<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->name('*.php');

$config = new PhpCsFixer\Config();
return $config
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        '@PHP70Migration' => true,
        '@PHP71Migration' => true,
        '@PHP73Migration' => true,
        '@PHP74Migration' => true,
        '@PHP80Migration' => true,
        '@PHP81Migration' => true,
        '@PHP82Migration' => true,
        '@PHP83Migration' => true,
        'modernize_strpos' => true,
        'no_empty_statement' => true,
        'fully_qualified_strict_types' => true,
        'no_unused_imports' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'heredoc_indentation' => ['indentation' => 'same_as_start'],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
