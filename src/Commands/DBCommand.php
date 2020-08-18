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

use ParagonIE\EasyDB\EasyDB;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Raúl Cruz <cruzcraul@gmail.com>
 */
class DBCommand extends BaseCommand
{
    /** @var \ParagonIE\EasyDB\EasyDB */
    protected $db;

    /**
     * @inheritdoc
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->db = \PhpCfdi\Efos\DB::getDB();
        
        parent::initialize($input, $output);
    }
}
