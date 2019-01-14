<?php
/**
 * This is the configuration file for the Yii2 unit tests.
 * You can override configuration values by creating a `config.local.php` file
 * and manipulate the `$config` variable.
 * For example to change PostgreSQL username and password your `config.local.php` should
 * contain the following:
 *
 * <?php
 * $config['databases']['pgsql']['username'] = 'yiitest';
 * $config['databases']['pgsql']['password'] = 'changeme';
 */

$config = [
    'databases' => [
        'sqlite' => [
            'dsn' => 'sqlite::memory:',
            'fixture' => __DIR__ . '/sqlite.sql',
        ],
        'pgsql' => [
            'dsn' => 'pgsql:host=localhost;port=5432;dbname=yiitest',
            'username' => 'postgres',
            'password' => 'postgres',
            'fixture' => __DIR__ . '/pgsql.sql',
        ],
    ],
];

if (is_file(__DIR__ . '/config.local.php')) {
    include(__DIR__ . '/config.local.php');
}

return $config;
