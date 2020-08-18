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
use PhpCfdi\Efos\Classes\EfosResource;

/**
 * The class is used to handle the 69B EFOS listing
 * 
 * @author Raúl Cruz <cruzcraul@gmail.com>
 */
class EfosResources 
{
    /** @var LoggerInterface */
    protected $logger;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        # code...
        $this->logger = $logger;
    }

    /**
     * Check for updates
     *
     * @return void
     */
    public function checkForUpdates()
    {
        $resources = (new EfosDBOperations())->getResources();
        foreach ($resources as $resource) {
            (new Efos($this->logger, new EfosResource($this->logger, $resource)))->run();
        }
    }

    /**
     * Find resources by RFC
     *
     * @param string $rfc
     * @return array
     */
    public function findByRFC(string $rfc): array
    {
        $resources = [];
        $data = (new EfosDBOperations())->getResourcesByRFC($rfc);
        foreach ($data as $resource) {
            $resource = new EfosRFCResource($resource);
            $resources[$resource->getResourceIndex()][] = $resource;
        }
        return $resources;
    }
}
