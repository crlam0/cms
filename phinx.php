<?php

require 'local/config.php';

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/migrations',
    ],
    'environments' => [
        'default_database' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => $DBHOST,
            'name' => $DBNAME,
            'user' => $DBUSER,
            'pass' => $DBPASSWD,
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ],
    ]
];


