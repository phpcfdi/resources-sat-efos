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

use PhpCfdi\Efos\DB;
use PhpCfdi\Efos\Reader\CsvReader;
use PhpCfdi\Efos\Classes\EfosResource;
use PhpCfdi\Efos\Contracts\EfosResourceInterface;

/**
 * Class to insert the data on database
 * 
 * @author Raúl Cruz <cruzcraul@gmail.com>
 */
class EfosDBOperations 
{
    /** @var string */
    const TABLE_NAME = 'resources_sat_efos';

    /** @var \ParagonIE\EasyDB\EasyDB */
    protected $db;

    /**
     * Maximum number of records to insert bulk data
     *
     * @var int
     */
    protected $max_bulk_inserts;

    /**
     * Constructor
     *
     * @param EfosResourceInterface $efos_resource
     * @param integer $max_bulk_inserts
     */
    public function __construct(int $max_bulk_inserts = 1000)
    {
        $this->max_bulk_inserts = $max_bulk_inserts;
        $this->db = DB::getDB();
    }

    /**
     * Populate from Reader
     *
     * @param \PhpCfdi\Efos\Reader\CsvReader $reader
     * @param \PhpCfdi\Efos\Contracts\EfosResourceInterface $efos_resource
     * @param integer $max_bulk_inserts
     * @return int 
     */
    public static function populateFromReader(CsvReader $reader, EfosResourceInterface $efos_resource, int $max_bulk_inserts = 1000) : int
    {
        return (new self($max_bulk_inserts))->doPopulateFromReader($reader, $efos_resource);
    }

    /**
     * Populate the resources from CsvReader 
     *
     * @param CsvReader $reader
     * @param EfosResourceInterface $efos_resource
     * @return integer
     */
    private function doPopulateFromReader(CsvReader $reader, EfosResourceInterface $efos_resource) : int
    {
        /** @var Aura\SqlQuery\Common\Insert */
        $insert = DB::newInsert(self::TABLE_NAME);
        
        $count = 1;
        $records_added = 0;
        foreach ($reader->getRecords() as $record) {

            // Add Row filtering only the fields needed
            $data = $record->filter($efos_resource->getColumnsRequired());

            $rfc_resource = new EfosRFCResource([
                'row_key' => md5($data['rfc']),
                'column_name' => $efos_resource->getColumnName(true),
                'body' => array_values($data)
            ]);

            $insert->addRow($rfc_resource->getRowToInsert());

            $records_added++;
            if ($count == $this->max_bulk_inserts) {
                $this->db->safeQuery($insert->getStatement(), $insert->getBindValues());
                $insert = DB::newInsert(self::TABLE_NAME);
                $count = 0;
            }
            $count++;
        }

        // Insert Remanent
        if ($insert->hasCols()) {
             $this->db->safeQuery($insert->getStatement(), $insert->getBindValues());
        }

        return $records_added;
    }

    /**
     * Update a resource
     *
     * @param EfosResourceInterface $efos_resource
     * @return void
     */
    public function updateResource(EfosResourceInterface $efos_resource): void
    {
        $update = (DB::getNewUpdate(self::TABLE_NAME))
            ->cols(['body' => ':body'])
            ->where('row_key = :row_key')
            ->where('reference_key = :reference_key')
            ->where('column_name = :column_name')
            ->bindValues([
                'body' => $efos_resource->getBody(true),
                'row_key' => $efos_resource->getRowKey(),
                'reference_key' => $efos_resource->getReferenceKey(),
                'column_name' => $efos_resource->getColumnName()
            ]);
    
        $this->db->safeQuery($update->getStatement(), $update->getBindValues());
    }

    /**
     * Insert a resource
     *
     * @param EfosResourceInterface $efos_resource
     * @return void
     */
    public function insertResource(EfosResourceInterface $efos_resource): void
    {
        $insert = (DB::newInsert(self::TABLE_NAME))
            ->addRow($efos_resource->getRowToInsert());

        $this->db->safeQuery($insert->getStatement(), $insert->getBindValues());
    }

    /**
     * Checks if resources exists en DB
     *
     * @param EfosResourceInterface $efos_resource
     * @return boolean
     */
    public function existsResource(EfosResourceInterface $efos_resource): bool
    {
        $select = (DB::newSelect(self::TABLE_NAME))
            ->cols(['COUNT(id) as total'])
            ->where('row_key = :row_key')
            ->where('reference_key = :reference_key')
            ->where('column_name = :column_name')
            ->bindValues([
                'row_key' => $efos_resource->getRowKey(),
                'row_key' => $efos_resource->getRowKey(),
                'reference_key' => $efos_resource->getReferenceKey(),
                'column_name' => $efos_resource->getColumnName()
            ]);

        $res = $this->db->safeQuery($select->getStatement(), $select->getBindValues());
        return $res[0]['total'] > 0;
    }

    /**
     * Get all resources
     *
     * @return array
     */
    public function getResources(): array
    {
        $select = (DB::newSelect(self::TABLE_NAME))
            ->cols(['*'])
            ->where('row_key = :row_key')
            ->where('reference_key = :reference_key')
            ->bindValues([
                'row_key' => md5('efos_resource'),
                'reference_key' => EfosResource::REFERENCE_KEY_RESOURCE,
            ]);

        // TODO: Add pagination
        return $this->db->safeQuery($select->getStatement(), $select->getBindValues());
    }

    /**
     * Find resources by RFC
     *
     * @param string $rfc
     * @return array
     */
    public function getResourcesByRFC(string $rfc): array
    {
        $select = (DB::newSelect(self::TABLE_NAME))
            ->cols(['*'])
            ->where('row_key = :row_key')
            ->where('reference_key = :reference_key')
            ->bindValues([
                'row_key' => md5($rfc),
                'reference_key' => EfosResource::REFERENCE_KEY_RFC,
            ]);

        // TODO: Add pagination
        return $this->db->safeQuery($select->getStatement(), $select->getBindValues());
    }

    /**
     * Find a resource
     *
     * @param string $row_key
     * @param integer $reference_key
     * @param string $column_name
     * @return array
     */
    public function findResource(string $row_key, int $reference_key, string $column_name): array
    {
        $select = (DB::newSelect(self::TABLE_NAME))
            ->cols(['*'])
            ->where('row_key = :row_key')
            ->where('reference_key = :reference_key')
            ->where('column_name = :column_name')
            ->bindValues([
                'row_key' => $row_key,
                'reference_key' => $reference_key,
                'column_name' => $column_name,
            ]);

        $rows = $this->db->safeQuery($select->getStatement(), $select->getBindValues());
        return empty($rows) ? [] : $rows[0];
        
    }
}
