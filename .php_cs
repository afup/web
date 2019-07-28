<?php

$finder = PhpCsFixer\Finder::create()
    ->name('*.php')
    ->in(__DIR__ . "/sources/AppBundle")
;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
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
    ])
    ->setFinder($finder)
;
