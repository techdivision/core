<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 7
 *
 * @author    Johann Zelger <j.zelger@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.techdivision.com
 */

declare(strict_types=1);

namespace TechDivision\Core\Adapter;

use Iterator;
use TechDivision\Core\File\CsvFileInterface;
use TechDivision\Core\File\SimpleCsvFile;

/**
 * CSV File InputAdapter implementation.
 *
 * @author    Johann Zelger <j.zelger@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.techdivision.com
 */
class CsvFileInputAdapter implements InputAdapterInterface
{
    /**
     * Holds a csv file implementation.
     *
     * @var CsvFileInterface
     */
    protected CsvFileInterface $csvFile;

    /**
     * Holds index information for fast search.
     *
     * @var array
     */
    protected array $index = [];

    /**
     * Holds data in cache if caching is enabled.
     *
     * @var array
     */
    protected array $cache = [];

    /**
     * The key where to generate the index for.
     *
     * @var null|string
     */
    protected $indexKey;

    /**
     * Caching flag for enable or disable caching functionality.
     *
     * @var bool
     */
    protected $caching = false;

    /**
     * CsvFileInputAdapter constructor.
     *
     * @param string $csvFilename
     * @param string $csvSeparator
     * @param null $indexKey
     * @param bool $caching
     */
    public function __construct(string $csvFilename, $csvSeparator = ";", $indexKey = null, $caching = false)
    {
        $this->csvFile = new SimpleCsvFile($csvFilename, $csvSeparator);
        $this->indexKey = $indexKey;
        $this->caching = $caching;
    }

    /**
     * Initialise adapter by generating csv index and caching if enabled.
     *
     * @return void
     */
    public function init(): void
    {
        $this->csvFile->open();
        if ($this->indexKey !== null) {
            $this->generateIndex($this->indexKey, $this->caching);
        }
    }

    /**
     * Generates the index for given key.
     *
     * @param string $indexKey
     * @param bool $caching
     * @return void
     */
    protected function generateIndex(string $indexKey, bool $caching): void
    {
        // go to the beginning of the file
        $this->csvFile->rewindToDataPosition();
        while (!$this->csvFile->eof()) {
            // get current file position
            $filePosition = $this->csvFile->tell();
            // read corresponding row
            if ($csvRow = $this->csvFile->getcsv()) {
                // save file position with related csv rows field value
                $this->index[$csvRow[$indexKey]][] = $filePosition;
                // cache complete csv row if enabled
                if ($caching === true) {
                    $this->cache[$filePosition] = $csvRow;
                }
            }
        }
    }

    /**
     * Returns all fields given in csv file.
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->csvFile->getHeaders();
    }

    /**
     * Returns data as iterator object for being able to stream data while iterating.
     *
     * @return Iterator
     */
    public function getData(): Iterator
    {
        return $this->csvFile;
    }

    /**
     * Finds data by given value for defined index key.
     *
     * @param string $keyValue
     * @return array
     */
    public function findData(string $keyValue): array
    {
        if (isset($this->index[$keyValue])) {
            // read rows by indexed file position
            return $this->findRowsByFilePositions($this->index[$keyValue]);
        }
        return [];
    }

    /**
     * Filters data by given value filter for defined index key.
     *
     * @param string $keyValueFilter
     * @return array
     */
    public function filterData(string $keyValueFilter): array
    {
        // filter array key by given value filter
        $filtered = array_filter($this->index, function ($k) use ($keyValueFilter) {
            return preg_match('/' . str_replace('/', '\/', $keyValueFilter) . '/', "$k");
        }, ARRAY_FILTER_USE_KEY);

        // init filtered rows variable
        $filteredRows = [];
        // iterate over all found filtered data
        foreach ($filtered as $filePosition) {
            // read rows by given file positions from index
            $filteredRows[] = $this->findRowsByFilePositions($filePosition);
        }
        // return filtered rows
        return $filteredRows;
    }

    /**
     * Returns data for given file positions array
     *
     * @param array $filePositions
     * @return array
     */
    protected function findRowsByFilePositions(array $filePositions): array
    {
        $rows = [];
        foreach ($filePositions as $position) {
            // check if cache is available and fill row with cached data
            if (isset($this->cache[$position])) {
                $row = $this->cache[$position];
                // if no cache is available read csv by position on demand
            } else {
                // go to indexed position
                $this->csvFile->seek($position);
                // add row appened with headers as array keys
                $row = $this->csvFile->getcsv();
            }
            $rows[] = $row;
        }
        // finally return all found rows
        return $rows;
    }
}
