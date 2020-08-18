<?php

declare(strict_types=1);

namespace PhpCfdi\Efos\Reader;

use League\Csv\Reader;

/** @internal */
class CsvContent
{
    public $headers = [];
    public $offsetHaeder = 3;

    /**
     * @var Reader
     */
    private $csvReader;

    /**
     * The $iterator will be used in a foreach loop to create MetadataItems
     * The first iteration must contain an array of header names that will be renames to lower case first letter
     * The next iterations must contain an array with data
     *
     * @param Reader
     */
    public function __construct(Reader $csvReader, array $headers = [])
    {
        $this->csvReader = $csvReader;
        $this->headers = $headers;
    }

    /**
     * @return CsvItem[]
     */
    public function eachItem()
    {
        $headers = $this->headers;
        $onFirstLine = true;
        // process content lines
        foreach ($this->csvReader as $index => $data) {
            if (! is_array($data) || 0 === count($data) || [null] === $data) {
                continue;
            }

            if ($index < $this->offsetHaeder) {
                continue;
            }

            if (!$headers && $onFirstLine) {
                $onFirstLine = false;
                $headers = array_map('lcfirst', $data);
                continue;
            }

            yield $this->createMetadataItem($headers, $data);
        }
    }

    /**
     * @param array $headers
     * @param array $values
     * @return CsvItem
     */
    public function createMetadataItem(array $headers, array $values): CsvItem
    {
        $countValues = count($values);
        $countHeaders = count($headers);
        if ($countHeaders > $countValues) {
            $values = array_merge($values, array_fill($countValues, $countHeaders - $countValues, ''));
        }
        if ($countValues > $countHeaders) {
            for ($i = 1; $i <= $countValues - $countHeaders; $i++) {
                $headers[] = sprintf('#extra-%02d', $i);
            }
        }
        return new CsvItem(array_combine($headers, $values) ?: []);
    }
}
