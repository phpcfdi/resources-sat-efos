<?php
 /*
 *  This file is part of the phpCfdi package.
 *  
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *  
 *  (c) 2020 phpCfdi
 *  
 */

return [
    'table_storage' => [
        'table_name' => 'doctrine_migration_efos',
        'version_column_name' => 'version',
        'version_column_length' => 1024,
        'executed_at_column_name' => 'executed_at',
        'execution_time_column_name' => 'execution_time',
    ],

    'migrations_paths' => [
        'PhpCfdi\Efos\Migrations' => '/EfosMigrations',
    ],

    'all_or_nothing' => true,
    'check_database_platform' => true,
];