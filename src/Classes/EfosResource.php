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
use PhpCfdi\Efos\Exceptions\DownloadException;
use PhpCfdi\Efos\Contracts\EfosResourceInterface;

/**
 * @author Raúl Cruz <cruzcraul@gmail.com>
 */
class EfosResource extends EfosAbstractResource implements EfosResourceInterface
{
    /** @var string */
    protected $new_etag;

    /** @var integer */
    protected $reference_key = self::REFERENCE_KEY_RESOURCE;
    
    /**
     * Constants
     */
    const BODY_COLUMN_NAME = 'column_name';
    const BODY_DESCRIPTION = 'description';
    const BODY_LAST_ETAG = 'last_etag';
    const BODY_URL = 'url';
    const BODY_HEADERS = 'headers';
    const BODY_COLUMNS_REQUIRED = 'columns_required';

    protected static $body_required_keys = [
        self::BODY_COLUMN_NAME,
        self::BODY_DESCRIPTION,
        self::BODY_LAST_ETAG,
        self::BODY_URL,
        self::BODY_HEADERS,
        self::BODY_COLUMNS_REQUIRED,
    ];

    /** @var LoggerInterface */
    protected $logger;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(LoggerInterface $logger, array $data)
    {
        parent::__construct($data);
        $this->logger = $logger;
        $this->http_client = new EfosHttpClient($logger, $this->getUrl());
    }

    /**
     * @inheritdoc
     */
    public function getBodyRequiredKeys() :array
    {
        return self::$body_required_keys;
    }

    /**
     * Returns the value of the last stored ETag
     *
     * @return string
     */
    public function getEtag(): string
    {
        return $this->getBodyValue(self::BODY_LAST_ETAG);
    }

    /**
     * Returns the URL of an EFOS resource
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->getBodyValue(self::BODY_URL);
    }

    /**
     * Return the headers of an EFOS resource
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return (array)$this->getBodyValue(self::BODY_HEADERS);
    }

    /**
     * Returns the columns required of an EFOS resource
     *
     * @param integer $num_columns
     * @return array
     */
    public function getColumnsRequired(int $num_columns = 0): array
    {
        $headers = (array)$this->getBodyValue(self::BODY_COLUMNS_REQUIRED);
        if ($num_columns > 0) {
            $headers = array_chunk($headers, $num_columns)[0];
        }
        return $headers;
    }

    /**
     * Returns the column name
     *
     * @param boolean $from_body
     * @return string
     */
    public function getColumnName(bool $from_body = true): string
    {
        if ($from_body) {
            return $this->getBodyValue(self::KEYNAME_COLUMN_NAME);
        }
        return parent::getColumnName();
    }

    /**
     * Set the Etag value
     *
     * @param string $etag
     * @return self
     */
    public function setEtag(string $etag)
    {
        $body = $this->getBody();
        $body[self::BODY_LAST_ETAG] = $etag;
        $this->setBody($body);
        return $this;
    }

    /**
     * Checks if resource has a new version
     *
     * @return boolean
     */
    public function hasNewVersion(): bool
    {
        $this->new_etag = $this->http_client->getEtag();
        if ($this->getEtag() != $this->new_etag) {
            return true;
        }
        return false;
    }

    /**
     * Handles the download of the resource.
     *
     * @throws DownloadException
     * @return string|null
     */
    public function download() : ?string
    {
        $this->logger->info("Downloading: " . $this->getUrl());
        try {
            $filename = $this->http_client->download();
            $this->logger->info("Download completed.");
            return $filename;
        } catch (DownloadException $th) {
            $this->logger->error('There was a problem downloading the resource: ' . $this->getUrl());
            return null;
        }
    }

    /**
     * Update the Etag
     *
     * @return self
     */
    public function updateETag()
    {
        $new_tag = $this->new_etag 
            ? $this->new_etag
            : $this->http_client->getEtag();
        $this->logger->info("Updating new ETag value: {$new_tag}");
        (new EfosDBOperations())->updateResource($this->setEtag($new_tag));

        return $this;
    }

    /**
     * Insert the resource in the DB
     *
     * @return void
     */
    public function insert(): void
    {
        $db_operations = new EfosDBOperations();
        $this->logger->info("Checking if the resource already exists in DB " . $this->getUrl() . "...");
        if ($db_operations->existsResource($this)) {
            $this->logger->info("Already exists, nothing to insert");
        } else {
            $this->logger->info("Not exists, inserting...");
            $db_operations->insertResource($this);
            $this->logger->info("Done.");
        }
    }

    /**
     * Find resource by index
     *
     * @param string $index
     * @return EfosResourceInterface
     */
    public static function findByIndex($index): EfosResourceInterface
    {
        list($row_key, $column_name) = explode(":", $index);
        $data = (new EfosDBOperations())->findResource(
            $row_key,
            self::REFERENCE_KEY_RESOURCE,
            $column_name
        );

        return new self(\PhpCfdi\Efos\Runner::getLogger(), $data);
    }

    /**
     * Returns the resources description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->getBodyValue(self::BODY_DESCRIPTION);
    }

}
