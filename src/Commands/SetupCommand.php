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

namespace PhpCfdi\Efos\Commands;

use Dotenv\Dotenv;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to setup EFOS library
 * @author Raúl Cruz <cruzcraul@gmail.com>
 */
class SetupCommand extends BaseCommand
{
    /**
     * @inheritcoc
     */
    protected static $defaultName = 'sat:efos-setup';

    /**
     * @inheritcoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Setup the EFOS Library');
    }

    /**
     * @inheritcoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->logger->info('Checking if .env file exists...');
        $base_path = $this->getBasePath();
        
        $dotenv = Dotenv::createUnsafeMutable($base_path);

        if (empty($dotenv->safeLoad())) {
            $this->logger->info('File not found');
            
            $content = '';
            $default_values = [
                'DB_USER' => 'root',
                'DB_PASS' => '',
                'DB_NAME' => 'efos',
                'DB_DRIVER' => 'pdo_mysql',
                'DB_HOST' => '127.0.0.1:3306',
            ];

            $this->logger->info('Creating new .env file...');
            foreach ($default_values as $key => $val) {
                $content .= "$key=$val\n";
            }
    
            file_put_contents($base_path . '/.env', $content);
            $this->logger->info('File created');
        } else {
            $this->logger->info('Found file, nothing to do');
        }
        $this->logger->info('Please make sure to configure the DB connection settings in the .env file');
        $this->showRunTimeExecution();
        return 0;
    }
}
