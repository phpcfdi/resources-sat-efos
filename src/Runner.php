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

use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\ErrorHandler;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use PhpCfdi\Efos\Commands\FindCommand;
use PhpCfdi\Efos\Commands\SetupCommand;
use Symfony\Component\Console\Application;
use PhpCfdi\Efos\Commands\CheckForUpdatesCommand;
use Doctrine\Migrations\Tools\Console\ConsoleRunner;
use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;

/**
 * The Runner class is used to create the Symfony Console application to perform EFOS related actions.
 * 
 * @author Raúl Cruz <cruzcraul@gmail.com>
 */
class Runner 
{
    /**
     * Efos runner version
     * 
     * @var string
     */
    private static $version = '1.0';

    /**
     * 
     * @return Application
     */
    public static function getApplication() : Application
    {
        self::validateInvoke();
        return self::registerCommands(self::createApplication(), self::getLogger());
    }

    /**
     * @return void
     */
    public static function validateInvoke() : void
    {
        if (!in_array(PHP_SAPI, ['cli', 'phpdbg', 'embed'], true)) {
            echo 'Warning: The console should be invoked via the CLI version of PHP, not the '.PHP_SAPI.' SAPI'.PHP_EOL;
        }
    }

    /**
     * @return void
     */
    public static function loadEnvironmentConfig() : void
    {
        $dotenv = Dotenv::createUnsafeMutable([
            __DIR__ . '/../',
            __DIR__ . '/../../../../',
        ]);
        $rslt = $dotenv->safeLoad();
        
        $logger = self::getLogger();
        if (empty($rslt)) {
            $logger->error('Environment file (.env) not found in root directory. Plase run "php vendor/bin/efos sat:efos-setup" command to generate it.');
            exit(-1);
        } else {
            try {
                $dotenv->required(['DB_USER', 'DB_PASS', 'DB_HOST', 'DB_NAME', 'DB_DRIVER']);
            } catch (\Throwable $th) {
                $logger->error($th->getMessage());
                exit(-1);
            }
        }
    }

    /**
     * @return LoggerInterface
     */
    public static function getLogger() : LoggerInterface
    {
        $output = "[%datetime%] %channel%.%level_name%: %message%\n";
        $formatter = new LineFormatter($output);

        $streamHandler = new StreamHandler('php://stdout', Logger::DEBUG);
        $streamHandler->setFormatter($formatter);

        $logger = new Logger('EFOS');
        $logger->pushHandler($streamHandler);

        (new ErrorHandler($logger))->registerErrorHandler();

        return $logger;
    }

    /**
     * @return Application
     */
    private static function createApplication() : Application
    {
        $application = ConsoleRunner::createApplication();
        $application->setName('EFOS (Empresa que Factura Operaciones Simuladas) command line interface');
        $application->setVersion('Version ' . self::$version);

        return $application;
    }

    /**
     * @param Application $application
     * @param LoggerInterface $logger
     * @return Application
     */
    public static function registerCommands(Application $application, LoggerInterface $logger) : Application
    {
        $commandLoader = new FactoryCommandLoader([
            SetupCommand::getDefaultName() => function () use ($logger) { return new SetupCommand($logger); },
            CheckForUpdatesCommand::getDefaultName() => function () use ($logger) { return new CheckForUpdatesCommand($logger); },
            FindCommand::getDefaultName() => function () use ($logger) { return new FindCommand($logger); },
        ]);
        $application->setCommandLoader($commandLoader);

        return $application;
    }
}
