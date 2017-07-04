<?php

$finder = PhpCsFixer\Finder::create()->in(__DIR__.'/src');

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@Symfony' => true,
        'binary_operator_spaces' => [
            'align_equals' => true,
            'align_double_arrow' => true,
        ],
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'combine_consecutive_unsets' => true,
        'no_useless_return' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
    ))
    ->setFinder($finder)
;