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

interface EfosBaseResourceInterface
{
    /**
     * Constants
     */
    const REFERENCE_KEY_RESOURCE = 1;
    const REFERENCE_KEY_RFC = 2;
    const KEYNAME_ROW_KEY = 'row_key';
    const KEYNAME_REFERENCE_KEY = 'reference_key';
    const KEYNAME_COLUMN_NAME = 'column_name';
    const KEYNAME_BODY = 'body';
    const KEYNAME_CREATED = 'created';

    /**
     * Returns the body required keys
     *
     * @return array
     */
    public function getBodyRequiredKeys(): array;

    /**
     * Returns the row key
     * 
     * @return string
     */
    public function getRowKey() : string;

    /**
     * Returns the reference key
     *
     * @return int
     */
    public function getReferenceKey() : int;

    /**
     * Returns the column name
     *
     * @return string
     */
    public function getColumnName() : string;

    /**
     * Returns the body
     *
     * @param boolean $encoded
     * @return array|string
     */
    public function getBody(bool $encoded = false);

    /**
     * Set the body data
     *
     * @param array $body
     * @return self
     */
    public function setBody(array $body);

    /**
     * Return the body value from a key
     *
     * @param string $key
     * @return mixed array|string|null
     */
    public function getBodyValue(string $key);

    /**
     * Return the data to insert
     *
     * @param array $body
     * @return array
     */
    public function getRowToInsert() : array;

    /**
     * Return created timestamp
     *
     * @return integer
     */
    public function getCreated(): int;

    /**
     * Get the resource index
     *
     * @return string
     */
    public function getResourceIndex(): string;
}