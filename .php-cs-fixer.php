<?php

$finder = (new PhpCsFixer\Finder())
    ->name('*.php')
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/db',
        __DIR__ . '/htdocs/pages',
        __DIR__ . '/sources',
        __DIR__ . '/tests',
    ])
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@PHP74Migration:risky' => true,
        'blank_line_after_opening_tag' => true,
        'concat_space' => ['spacing' => 'one'],
        'no_alias_functions' => true,
        'native_function_casing' => true,
        'no_blank_lines_after_class_opening' => true,
        'ordered_imports' => true,
        'phpdoc_no_access' => true,
        'no_leading_import_slash' => true,
        'self_accessor' => true,
        'array_syntax' => ['syntax' => 'short'],
        'cast_spaces' => true,
        'no_unused_imports' => true,
        'no_whitespace_in_blank_line' => true,
        'method_argument_space' => false,
        'statement_indentation' => false
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/var/.php-cs-fixer.cache')
;
