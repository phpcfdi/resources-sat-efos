<?php

declare(strict_types=1);

namespace PhpCfdi\Efos\Reader;

use Countable;
use RuntimeException;
use League\Csv\Reader;

/** @internal */
abstract class AbstractReader implements Countable
{
    protected $csv;
    protected $headers;

    /** @var bool */
    private $removeOnDestruct = false;

    /** @var string */
    private $filename;

    /**
     * @param string $filename
     * @throws RuntimeException Could not open zip file
     */
    final public function __construct(string $filename, array $headers = [])
    {
        if (!file_exists($filename)) {
            throw new RuntimeException("Filename '{$filename}' not foud");
            
        }
        $this->headers = $headers;
        $this->filename = $filename;
        $this->csv = Reader::createFromPath($this->filename, 'r');
        $this->csv->addStreamFilter('convert.iconv.ISO-8859-3/UTF-8');
    }

    public function __destruct()
    {
        // destruct does not enter if the object was not fully constructed
        if ($this->removeOnDestruct) {
            @unlink($this->filename);
        }
        $this->csv = null;
    }

    public function count(): int
    {
        return $this->csv->count();
    }    
}
