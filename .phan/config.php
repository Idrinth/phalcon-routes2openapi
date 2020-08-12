<?php

return [
    'pretend_newer_core_functions_exist' => false,
    'guess_unknown_parameter_type_using_default' => true,
    'allow_overriding_vague_return_types' => true,
    'infer_default_properties_in_construct' => true,
    'enable_extended_internal_return_type_plugins' => true,
    'dead_code_detection' => true,
    'unused_variable_detection' => true,
    'force_tracking_references' => true,
    'redundant_condition_detection' => true,
    'error_prone_truthy_condition_detection' => true,
    'warn_about_redundant_use_namespaced_class' => true,
    'simplify_ast' => true,
    'generic_types_enabled' => true,
    'exclude_file_regex' => '@^vendor/phalcon/ide-stubs/|vendor/.*/Test.php@',
    'enable_include_path_checks' => true,
    'directory_list' => [
        'src',
        'vendor',
        // @todo reactivate
        //'test',
    ],
    'analyzed_file_extensions' => ['php'],
    'exclude_analysis_directory_list' => [
        'vendor/'
    ],
    'skip_slow_php_options_warning' => false,
    'ignore_undeclared_functions_with_known_signatures' => false,
    'plugin_config' => [
        'has_phpdoc_check_duplicates' => true,
        'empty_statement_list_ignore_todos' => true,
        'infer_pure_methods' => true,
        'regex_warn_if_newline_allowed_at_end' => true,
    ],
    'suppress_issue_types' => [
        'PhanPluginRedundantReturnComment',
        'PhanPluginRedundantMethodComment',
        'PhanUnreferencedPublicMethod',
        'PhanPluginDescriptionlessCommentOnPrivateProperty',
        'PhanPluginCanUsePHP71Void',
        'PhanUnreferencedClosure',
        'PhanPluginPossiblyStaticPrivateMethod',
        'PhanPluginPossiblyStaticClosure',
        //@ todo reactivate
        
    ],
    'plugins' => [
        'AlwaysReturnPlugin',
        'DollarDollarPlugin',
        'UnreachableCodePlugin',
        'DuplicateArrayKeyPlugin',
        'PrintfCheckerPlugin',
        'PHPUnitAssertionPlugin',
        'UseReturnValuePlugin',
        'UnknownElementTypePlugin',
        'DuplicateExpressionPlugin',
        'WhitespacePlugin',
        'InlineHTMLPlugin',
        'PossiblyStaticMethodPlugin',
        'HasPHPDocPlugin',
        'PHPDocToRealTypesPlugin',
        'PHPDocRedundantPlugin',
        'PreferNamespaceUsePlugin',
        'EmptyStatementListPlugin',
        'EmptyMethodAndFunctionPlugin',
        'LoopVariableReusePlugin',
        'RedundantAssignmentPlugin',
        'StrictComparisonPlugin',
        'StrictLiteralComparisonPlugin',
        'ShortArrayPlugin',
        'SimplifyExpressionPlugin',
        'RemoveDebugStatementPlugin',
    ],
];
