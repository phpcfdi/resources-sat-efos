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

use PhpCfdi\Efos\Classes\EfosResources;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to check for updates to EFOS resources
 * @author Raúl Cruz <cruzcraul@gmail.com>
 */
class CheckForUpdatesCommand extends DBCommand
{
    /**
     * @inheritcoc
     */
    protected static $defaultName = 'sat:efos-check-for-updates';

    /**
     * @inheritcoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Check if there is a new version available from EFOS resources');
    }

    /**
     * @inheritcoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        (new EfosResources($this->logger))->checkForUpdates();
        $this->showRunTimeExecution();
        return 0;
    }
}