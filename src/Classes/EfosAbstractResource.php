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

use PhpCfdi\Efos\Exceptions\ResourceExcepetion;
use PhpCfdi\Efos\Contracts\EfosBaseResourceInterface;


/**
 * @author Raúl Cruz <cruzcraul@gmail.com>
 */
abstract class EfosAbstractResource implements EfosBaseResourceInterface
{
    /** @var array */
    protected static $valid_reference_keys = [
        self::REFERENCE_KEY_RFC,
        self::REFERENCE_KEY_RESOURCE,
    ];

    /** @var array */
    protected static $required_keys = [
        self::KEYNAME_ROW_KEY,
        self::KEYNAME_COLUMN_NAME,
        self::KEYNAME_BODY
    ];

    /** @var array */
    protected $data;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->validate();
    }

    /**
     * Validate the resource structure
     *
     * @return void
     */
    public function validate()
    {
        $this->validateRequiredKeys();
        $this->validateBodyRequiredKeys();
        $this->validateReferenceKeyProperty();
    }

    /**
     * Validate reference key
     *
     * @return void
     */
    public function validateReferenceKeyProperty(): void
    {
        $property = self::KEYNAME_REFERENCE_KEY;
        if (!property_exists($this, $property) || !in_array($this->$property, self::$valid_reference_keys)) {
            $error_message = " The reference_key property must be declared in the class and must have an valid integer value.";
            throw new \Exception($error_message);
        }
    }

    /**
     * Validate Keys
     *
     * @param array $required_keys
     * @param array $data
     * @param string $error_message
     * @return void
     */
    public function validateKeys(array $required_keys, array $data, string $error_message)
    {
        foreach ($required_keys as $key) {
            if (!array_key_exists($key, $data)) {
                $error_message .= " The required keys are: " . implode(' | ', $required_keys);
                throw new ResourceExcepetion($error_message);
            }
        }
    }

    /**
     * Validate required keys
     *
     * @throws ResourceExcepetion
     * @return void
     */
    private function validateRequiredKeys() :void
    {
        $mesage = "One or more keys are missing.";
        $this->validateKeys($this->getRequiredKeys(), $this->data, $mesage);
    }

    /**
     * Validate body required keys
     *
     * @throws ResourceExcepetion
     * @return void
     */
    private function validateBodyRequiredKeys() :void
    {
        $mesage = "One or more keys in the resource body are missing.";
        $this->validateKeys($this->getBodyRequiredKeys(), $this->getBody(), $mesage);
    }

    /**
     * Returns the required keys
     *
     * @return array
     */
    public function getRequiredKeys() : array
    {
        return self::$required_keys;
    }

    /**
     * Returns the row key
     *
     * @return string
     */
    public function getRowKey() : string
    {
        return $this->data[self::KEYNAME_ROW_KEY];
    }


    /**
     * Returns the reference key
     *
     * @return int
     */
    public function getReferenceKey() : int
    {
        $property = self::KEYNAME_REFERENCE_KEY;
        return $this->$property;
    }

    /**
     * Returns the column name
     *
     * @return string
     */
    public function getColumnName() : string
    {
        return $this->data[self::KEYNAME_COLUMN_NAME];
    }

    /**
     * Returns the body
     *
     * @return array|string
     */
    public function getBody(bool $encoded = false)
    {
        $body = $this->data[self::KEYNAME_BODY];
        if (is_string($body)) {
            $body = json_decode($body, true);
        }

        return $encoded ? json_encode($body) : $body;
    }

    /**
     * Set the body data
     *
     * @param array $body
     * @return self
     */
    public function setBody(array $body)
    {
        $this->data[self::KEYNAME_BODY] = $body;
        return $this;
    }

    /**
     * Return the body value from a key
     *
     * @param string $key
     * @return mixed array|string|null
     */
    public function getBodyValue(string $key)
    {
        return $this->getBody()[$key] ?? null;
    }

    /**
     * Return the data to insert
     *
     * @param array $body
     * @return array
     */
    public function getRowToInsert() : array
    {
        return [
            'row_key' => $this->getRowKey(),
            'reference_key' => $this->getReferenceKey(),
            'column_name' => $this->getColumnName(),
            'body' => json_encode($this->getBody()),
            'created' => time(),
        ];
    }

    /**
     * Return created timestamp
     *
     * @return integer
     */
    public function getCreated(): int
    {
        return (int)$this->data[self::KEYNAME_CREATED];
    }

    /**
     * Get the resource index
     *
     * @return string
     */
    public function getResourceIndex(): string
    {
        return md5('efos_resource') . ':' 
            . $this->getColumnName();
    }
}
