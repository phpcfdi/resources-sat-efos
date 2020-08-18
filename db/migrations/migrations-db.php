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

use PhpCfdi\Efos\Runner;
use Doctrine\DBAL\DriverManager;

Runner::loadEnvironmentConfig();

return DriverManager::getConnection([
    'dbname' => getenv('DB_NAME'),
    'user' => getenv('DB_USER'),
    'password' => getenv('DB_PASS'),
    'host' => getenv('DB_HOST'),
    'driver' => getenv('DB_DRIVER'),
    'charset' => 'utf8',
    'driverOptions'	=> array('1002'=> "SET NAMES 'UTF8' COLLATE 'utf8_general_ci'")
]);
