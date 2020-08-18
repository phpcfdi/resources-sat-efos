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

/**
 * @author Raúl Cruz <cruzcraul@gmail.com>
 */
class EfosRFCResource extends EfosAbstractResource
{
    protected $reference_key = self::REFERENCE_KEY_RFC;

    public function getBodyRequiredKeys(): array
    {
        return [];
    }
}
