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

use Monolog\Handler\IFTTTHandler;
use Monolog\Logger;
use PhpCfdi\Efos\Classes\EfosResource;
use PhpCfdi\Efos\Classes\EfosResources;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to find EFOS resources by RFC
 * @author Raúl Cruz <cruzcraul@gmail.com>
 */
class FindCommand extends DBCommand
{
    /**
     * @inheritcoc
     */
    protected static $defaultName = 'sat:efos-find';

    /**
     * @inheritcoc
     */
    protected function configure()
    {
        $this
            ->setDescription('The RFC to find')
            ->addArgument('rfc', InputArgument::REQUIRED, 'RFC')
            ->addOption('num-columns', 'nc', InputOption::VALUE_OPTIONAL, 'RFC', 4)
        ;
    }

    /**
     * @inheritcoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $rfc = $input->getArgument('rfc');
        $num_columns = (int)$input->getOption('num-columns');
        
        $tables = [];
        $resources_grouped = (new EfosResources($this->logger))->findByRFC($rfc);
        foreach ($resources_grouped as $resource_index => $items ) {
            $resource = EfosResource::findByIndex($resource_index);
            $headers = $resource->getColumnsRequired($num_columns);
            $table['headers'] = $headers;
            $table['title'] = $resource->getDescription();
            foreach ($items as $item) {
                $table['rows'][] = array_chunk($item->getBody(), $num_columns)[0];
            }
            $tables[] = $table;
        }

        foreach ($tables as $table) {
            echo "\n";
            $output_table = new Table($output);
            $output_table
                ->setHeaderTitle("Resultados en: " . $table['title'])
                ->setColumnMaxWidth(1, 80)
                ->setHeaders($table['headers'])
                ->setRows($table['rows'])
            ;
            $output_table->render();
        }

        if (empty($resources_grouped)) {
            $io = new SymfonyStyle($input, $output);
            $io->success('RFC not found.');
        }

        echo "\n";
        $this->showRunTimeExecution();
        echo "\n";
        return Command::SUCCESS;
    }
}