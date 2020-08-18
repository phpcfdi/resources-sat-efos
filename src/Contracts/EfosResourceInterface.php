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

namespace PhpCfdi\Efos\Contracts;

interface EfosResourceInterface extends EfosBaseResourceInterface
{
    /**
     * Returns the value of the ETag
     *
     * @return string
     */
    public function getEtag(): string;

    /**
     * Set the Etag value
     *
     * @param string $etag
     * @return self
     */
    public function setEtag(string $etag);

    /**
     * Returns the URL of an EFOS resource
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Return the headers of an EFOS resource
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Returns the columns required of an EFOS resource to store
     *
     * @param integer $num_columns
     * @return array
     */
    public function getColumnsRequired(int $num_columns = 0): array;

    /**
     * Checks if resource has a new version
     *
     * @return boolean
     */
    public function hasNewVersion(): bool;

    /**
     * Handles the download of the resource.
     *
     * @throws DownloadException
     * @return string|null
     */
    public function download() : ?string;

    /**
     * Update the Etag
     *
     * @return self
     */
    public function updateETag();

    /**
     * Returns the resources description
     *
     * @return string
     */
    public function getDescription(): string;
}