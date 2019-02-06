<?php

require 'include/config/config.local.php';

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/migrations',
    ],
    'environments' => [
        'default_database' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => $DBNAME,
            'user' => $DBUSER,
            'pass' => $DBPASSWD,
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ],
    ]
];


