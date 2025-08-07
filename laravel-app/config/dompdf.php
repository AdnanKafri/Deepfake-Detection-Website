<?php

return [

    'show_warnings' => false,   // Throw an Exception on warnings from dompdf

    'public_path' => null,  // Override the public path if needed

    'convert_entities' => true,

    'options' => [

        'font_dir' => storage_path('fonts'), // advised by dompdf (https://github.com/dompdf/dompdf/pull/782)

        'font_cache' => storage_path('fonts'),

        'temp_dir' => sys_get_temp_dir(),

        'chroot' => realpath(base_path()),

        /**
         * Protocol whitelist
         *
         * Protocols and PHP wrappers allowed in URIs, and the validation rules
         * that determine if a resouce may be loaded. Full support is not guaranteed
         * for the protocols/wrappers specified
         * by this array.
         *
         * @var array
         */
        'allowed_protocols' => [
            'data://' => ['rules' => []],
            'file://' => ['rules' => []],
            'http://' => ['rules' => []],
            'https://' => ['rules' => []],
        ],

        /**
         * Operational artifact (log files, temporary files) path validation
         */
        'artifactPathValidation' => null,

        /**
         * @var string
         */
        'log_output_file' => null,

        /**
         * Whether to enable font subsetting or not.
         */
        'enable_font_subsetting' => false,


        'pdf_backend' => 'CPDF',


        'default_media_type' => 'screen',

        'default_paper_size' => 'a4',

        'default_paper_orientation' => 'portrait',

        'default_font' => 'cairo',

        'custom_font_dir' => resource_path('fonts/'), // مجلد الخطوط المخصصة

        'custom_font_data' => [
            'cairo' => [
                'R'  => 'Cairo-Regular.ttf',
                'B'  => 'Cairo-Bold.ttf',
                'useOTL' => 0xFF,
                'useKashida' => 75,
            ],
            'amiri' => [
                'R' => 'Amiri-Regular.ttf',
                'B' => 'Amiri-Bold.ttf',
                'useOTL' => 0xFF,
                'useKashida' => 75,
            ]
        ],

        'dpi' => 96,


        'enable_php' => false,

        'enable_javascript' => true,


        'enable_remote' => false,

        'allowed_remote_hosts' => null,


        'font_height_ratio' => 1.1,


        'enable_html5_parser' => true,
    ],

];
