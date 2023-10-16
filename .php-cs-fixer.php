<?php

require __DIR__ . '/tools/php-cs-fixer/ClarifyingParenthesesAroundComparisonsFixer.php';

$finder = PhpCsFixer\Finder
    ::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

$config = new PhpCsFixer\Config();
$config->registerCustomFixers([
    new Hereldar\Tools\PhpCsFixer\ClarifyingParenthesesAroundComparisonsFixer(),
]);
$config->setFinder($finder);
$config->setRules([
    '@PHP81Migration' => true,
    '@PER-CS' => true,
    'align_multiline_comment' => true,
    'array_indentation' => true,
    'backtick_to_shell_exec' => true,
    'binary_operator_spaces' => true,
    'blank_line_before_statement' => ['statements' => ['case', 'declare', 'default', 'phpdoc', 'do', 'exit', 'for', 'foreach', 'goto', 'if', 'include', 'include_once', 'require', 'require_once', 'return', 'switch', 'throw', 'try', 'while', 'yield', 'yield_from']],
    'braces_position' => ['allow_single_line_anonymous_functions' => true, 'allow_single_line_empty_anonymous_classes' => true],
    'cast_spaces' => true,
    'class_attributes_separation' => ['elements' => ['method' => 'one', /*'case' => 'one'*/]],
    'class_reference_name_casing' => true,
    'combine_consecutive_issets' => true,
    'combine_consecutive_unsets' => true,
    'concat_space' => ['spacing' => 'one'],
    'declare_parentheses' => true,
    'echo_tag_syntax' => true,
    'empty_loop_body' => ['style' => 'braces'],
    'empty_loop_condition' => true,
    'escape_implicit_backslashes' => true,
    'explicit_string_variable' => true,
    'fully_qualified_strict_types' => true,
    'general_phpdoc_tag_rename' => ['replacements' => ['inheritDocs' => 'inheritDoc']],
    'include' => true,
    'increment_style' => true,
    'integer_literal_case' => true,
    'lambda_not_used_import' => true,
    'linebreak_after_opening_tag' => true,
    'magic_constant_casing' => true,
    'method_chaining_indentation' => true,
    'multiline_comment_opening_closing' => true,
    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    'native_function_casing' => true,
    'native_type_declaration_casing' => true,
    'no_alias_language_construct_call' => true,
    'no_alternative_syntax' => true,
    'no_binary_string' => true,
    'no_blank_lines_after_phpdoc' => true,
    'no_empty_comment' => true,
    'no_empty_phpdoc' => true,
    'no_empty_statement' => true,
    'no_extra_blank_lines' => ['tokens' => ['attribute', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'square_brace_block', 'switch', 'throw', 'use']],
    'no_leading_namespace_whitespace' => true,
    'no_mixed_echo_print' => true,
    'no_multiline_whitespace_around_double_arrow' => true,
    'no_short_bool_cast' => true,
    'no_singleline_whitespace_before_semicolons' => true,
    'no_spaces_around_offset' => true,
    'no_superfluous_phpdoc_tags' => ['remove_inheritdoc' => true],
    'no_trailing_comma_in_singleline' => true,
    'no_unneeded_braces' => ['namespaces' => true],
    'no_unneeded_control_parentheses' => ['statements' => ['break', 'clone', 'continue', 'echo_print', 'return', 'switch_case', 'yield', 'yield_from']],
    'no_unneeded_import_alias' => true,
    'no_unused_imports' => true,
    'object_operator_without_whitespace' => true,
    'operator_linebreak' => ['position' => 'beginning'],
    'ordered_class_elements' => true,
    'ordered_imports' => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
    'php_unit_fqcn_annotation' => true,
    'php_unit_method_casing' => true,
    'phpdoc_align' => ['align' => 'left'],
    'phpdoc_annotation_without_dot' => true,
    'phpdoc_indent' => true,
    'phpdoc_inline_tag_normalizer' => true,
    'phpdoc_no_access' => true,
    'phpdoc_no_alias_tag' => ['replacements' => ['type' => 'var', 'link' => 'see']],
    'phpdoc_no_package' => true,
    'phpdoc_order' => true,
    'phpdoc_order_by_value' => true,
    'phpdoc_return_self_reference' => true,
    'phpdoc_scalar' => true,
    'phpdoc_separation' => ['groups' => [
        ['deprecated', 'link', 'see', 'since'],
        ['author', 'copyright', 'license'],
        ['category', 'package', 'subpackage'],
        ['property', 'property-read', 'property-write', 'phpstan-property', 'phpstan-property-read', 'phpstan-property-write', 'psalm-property', 'psalm-property-read', 'psalm-property-write'],
        ['param', 'phpstan-param', 'psalm-param'],
        ['throws', 'phpstan-throws', 'psalm-throws'],
        ['return', 'phpstan-return', 'psalm-return'],
    ]],
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_summary' => true,
    'phpdoc_tag_type' => true,
    'phpdoc_to_comment' => true,
    'phpdoc_trim' => true,
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'phpdoc_types' => true,
    'phpdoc_types_order' => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
    'phpdoc_var_annotation_correct_order' => true,
    'phpdoc_var_without_name' => true,
    'protected_to_private' => true,
    'semicolon_after_instruction' => true,
    'simple_to_complex_string_variable' => true,
    'single_class_element_per_statement' => true,
    //'single_line_comment_spacing' => true,
    'single_line_comment_style' => true,
    'single_quote' => true,
    'single_space_around_construct' => true,
    'space_after_semicolon' => true,
    'standardize_increment' => true,
    'standardize_not_equals' => true,
    'switch_continue_to_break' => true,
    'trailing_comma_in_multiline' => true,
    'trim_array_spaces' => true,
    'type_declaration_spaces' => true,
    'types_spaces' => true,
    'unary_operator_spaces' => true,
    'whitespace_after_comma_in_array' => true,
    'Hereldar/clarifying_parentheses_around_comparisons' => true,
]);

return $config;
