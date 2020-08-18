<?php

declare(strict_types=1);

namespace PhpCfdi\Efos\Reader;

/**
 * Csv DTO object
 */
class CsvItem
{
    /** @var array<string, string> */
    private $data;

    /**
     * MetadataItem constructor.
     *
     * @param array<string, string> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __get(string $name): string
    {
        return $this->get($name);
    }

    /** @return array<string, string> */
    public function all(): array
    {
        return $this->data;
    }

    public function get(string $key): string
    {
        return $this->data[$key] ?? '';
    }

    /** @return array<string, string> */
    public function filter(array $keys)
    {
        $data = [];
        foreach ($keys as $key) {
            if (isset($this->data[$key])) {
                $data[$key] = $this->data[$key];
            }
        }
        return $data;
    }
}
