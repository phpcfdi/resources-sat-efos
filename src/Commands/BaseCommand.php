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

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Abstrac class to commands
 * @author Raúl Cruz <cruzcraul@gmail.com>
 */
abstract class BaseCommand extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var LoggerInterface
     */
    public $logger;

    protected $time_start;
    protected $time_end;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->startRuntime();
    }

    /**
     * Stores the start of command execution
     *
     * @return void
     */
    protected function startRuntime()
    {
        $this->time_start = microtime(true);
        $this->logger->info("Starting process.");
    }

    /**
     * Displays the summary of the command execution time
     *
     * @return void
     */
    protected function showRunTimeExecution()
    {
        $this->time_end = microtime(true);
        $execution_time = gmdate('H:i:s', (int)ceil($this->time_end - $this->time_start));
        $this->logger->info("Process completed.");
        $this->logger->info("Time execution: {$execution_time} seconds");
    }

    /**
     * Return the base path of project
     *
     * @return string
     */
    public function getBasePath() : string
    {
        $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
        return dirname($reflection->getFileName(), 3);
    }
}
