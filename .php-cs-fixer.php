<?php

use PhpCsFixerCustomFixers\Fixer\MultilinePromotedPropertiesFixer;

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
    ->registerCustomFixers(new PhpCsFixerCustomFixers\Fixers())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS3x0' => true,
        '@PHP7x4Migration:risky' => true,
        'no_alias_functions' => true,
        'native_function_casing' => true,
        'phpdoc_no_access' => true,
        'self_accessor' => true,
        'no_unused_imports' => true,
        'method_argument_space' => false,
        'statement_indentation' => false,
        'multiline_promoted_properties' => [
            'keep_blank_lines' => true,
            'minimum_number_of_parameters' => 2,
        ],
        'type_declaration_spaces' => true,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/var/.php-cs-fixer.cache')
;
