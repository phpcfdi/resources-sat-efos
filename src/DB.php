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

declare( strict_types = 1 );

namespace PhpCfdi\Efos;

use Aura\SqlQuery\QueryFactory;

/**
 * @author Raúl Cruz <cruzcraul@gmail.com>
 */
class DB 
{
    /**
     * Return new SELECT object
     *
     * @param string $table
     * @return Aura\SqlQuery\Common\Select
     */
    public static function newSelect(string $table) : \Aura\SqlQuery\Common\Select
    {
        return self::getQueryFactory()->newSelect()->from($table);
    }

    /**
     * Return new INSERT object
     *
     * @param string $table
     * @return Aura\SqlQuery\Common\Insert
     */
    public static function newInsert(string $table) : \Aura\SqlQuery\Common\Insert
    {
        return self::getQueryFactory()->newInsert()->into($table);
    }

    /**
     * Return new UPDATE object
     *
     * @param string $table
     * @return Aura\SqlQuery\Common\Update
     */
    public static function getNewUpdate(string $table) : \Aura\SqlQuery\Common\Update
    {
        return self::getQueryFactory()->newUpdate()->table($table);
    }

    /**
     * Returns the QueryFactory
     *
     * @return \Aura\SqlQuery\QueryFactory
     */
    public static function getQueryFactory() : \Aura\SqlQuery\QueryFactory
    {
        // TODO: Support others engines
        return new QueryFactory('mysql');
    }

    /**
     * @return \ParagonIE\EasyDB\EasyDB
     */
    public static function getDB($db_engine = 'mysql') : \ParagonIE\EasyDB\EasyDB
    {
        $logger = \PhpCfdi\Efos\Runner::getLogger();
        \PhpCfdi\Efos\Runner::loadEnvironmentConfig();
        
        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $db_user = getenv('DB_USER');
        $db_pass = getenv('DB_PASS');

        switch ($db_engine) {
            case 'mysql':
                $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8";
                break;
            
            default:
                // TODO: Add support for others engines
                throw new \Exception("DB Engine not supported", 1);
                break;
        }

        try {
            $db = new \ParagonIE\EasyDB\EasyDB(new \PDO($dsn, $db_user, $db_pass));
        } catch (\Throwable $th) {
            $logger->error($th->getMessage());
            exit(-1);
        }

        return $db;
    }
}