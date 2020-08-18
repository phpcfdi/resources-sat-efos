<?php

declare(strict_types=1);

namespace PhpCfdi\Efos\Reader;

class CsvReader extends AbstractReader
{
    /**
     * @return CsvItem[]
     */
    public function getRecords()
    {
        $reader = new CsvContent($this->csv, $this->headers);
        foreach ($reader->eachItem() as $item) {
            yield $item;
        }
    }
}
