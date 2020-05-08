<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src')
    ->exclude('vendor');

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2'                               => true,
        'single_quote'                        => true,
        'function_typehint_space'             => true,
        'hash_to_slash_comment'               => true,
        'method_separation'                   => true,
        'no_blank_lines_after_phpdoc'         => true,
        'no_blank_lines_before_namespace'     => true,
        'no_unused_imports'                   => true,
        'no_useless_else'                     => true,
        'phpdoc_align'                        => true,
        'phpdoc_order'                        => true,
        'phpdoc_scalar'                       => true,
        'pre_increment'                       => true,
        'short_scalar_cast'                   => true,
        'space_after_semicolon'               => true,
        'ternary_operator_spaces'             => true,
        'trailing_comma_in_multiline_array'   => true,
        'semicolon_after_instruction'         => true,
        'trim_array_spaces'                   => true,
        'whitespace_after_comma_in_array'     => true,
        'phpdoc_add_missing_param_annotation' => true,
        'binary_operator_spaces'              => ['align_equals' => false, 'align_double_arrow' => true],
        'concat_space'                        => ['spacing' => 'one'],
        'array_syntax'                        => ['syntax' => 'short'],
    ])
    ->setFinder($finder);
