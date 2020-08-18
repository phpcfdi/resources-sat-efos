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

namespace PhpCfdi\Efos\Classes;

use Psr\Log\LoggerInterface;
use PhpCfdi\Efos\Reader\CsvReader;
use PhpCfdi\Efos\Exceptions\DownloadException;
use PhpCfdi\Efos\Contracts\EfosResourceInterface;

class Efos
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var \PhpCfdi\Efos\Contracts\EfosResourceInterface */
    protected $efos_resource;

    /** @var string */
    protected $url;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger, EfosResourceInterface $efos_resource)
    {
        $this->logger = $logger;
        $this->efos_resource = $efos_resource;
        $this->url = $efos_resource->getUrl();
    }

    /**
     * Run the process
     *
     * @return void
     * @author Raúl Cruz <cruzcraul@gmail.com>
     */
    public function run() : void
    {
        $etag = $this->efos_resource->getEtag();
        $this->logger->info("Checking for updates for: {$this->url}");
        $this->logger->info("Last Etag: {$etag}");
        
        if ($this->efos_resource->hasNewVersion()) {
            $this->logger->info("Found new version of EFOS resource.");
            
            $filename = $this->efos_resource->download();
            if ($filename) {
                $this->populate(new CsvReader($filename, $this->efos_resource->getHeaders()));
                $this->efos_resource->updateEtag();
            }
        } else {
            $this->logger->info("No new version found, nothing to do.");
        }
    }

    /**
     * Populate the table
     * 
     * @param CsvReader $reader
     * @return self
     */
    public function populate(CsvReader $reader) : self
    {
        $this->logger->info("Inserting data...");
        $records_added = EfosDBOperations::populateFromReader($reader, $this->efos_resource);
        $this->logger->info("Data insertion completed, {$records_added} records were added.");
        
        return $this;
    }
}
