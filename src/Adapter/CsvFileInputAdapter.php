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
 * @link      https://github.com/techdivision/core
 * @link      https://www.techdivision.com
 */

declare(strict_types=1);

namespace TechDivision\Core\Adapter;

/**
 * Class CsvFileInputAdapter
 * @package TechDivision\Core\Adapter
 */
class CsvFileInputAdapter implements InputAdapterInterface
{
    /**
     * @var CsvFileInterface
     */
    protected $csvFile;

    /**
     * @var array
     */
    protected $index = [];

    /**
     * CsvFileInputAdapter constructor.
     * @param string $csvFilename
     * @param string $csvSeparator
     * @param null $indexKey
     */
    public function __construct(string $csvFilename, $csvSeparator = ";", $indexKey = null)
    {
        $this->csvFile = new SimpleCsvFile($csvFilename, $csvSeparator);
        if ($indexKey !== null) {
            $this->generateIndex($indexKey);
        }
    }

    /**
     * @param $indexKey
     * @return void
     */
    protected function generateIndex($indexKey)
    {
        // go to the beginning of the file
        $this->csvFile->rewindToDataPosition();
        while (!$this->csvFile->eof()) {
            // get current file position
            $filePosition = $this->csvFile->tell();
            // read corresponding row
            $csvRow = $this->csvFile->getcsv();
            // save file position with related csv rows field value
            $this->index[$csvRow[$indexKey]][] = $filePosition;
        }
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->csvFile->getHeaders();
    }

    /**
     * @return \Iterator|CsvFileInterface|SimpleCsvFile
     */
    public function getData()
    {
        return $this->csvFile;
    }

    /**
     * @param string $keyValue
     * @return \Iterator
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
     * @param string $keyValueFilter
     * @return \Iterator
     */
    public function filterData(string $keyValueFilter): array
    {
        // filter array key by given value filter
        $filtered = array_filter($this->index, function ($k) use ($keyValueFilter) {
            return preg_match('/' . str_replace('/', '\/', $keyValueFilter) . '/', $k);
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
     * @param array $filePositions
     * @return array|mixed
     */
    protected function findRowsByFilePositions(array $filePositions)
    {
        $rows = [];
        foreach ($filePositions as $position) {
            // go to indexed position
            $this->csvFile->seek($position);
            // add row appened with headers as array keys
            $rows[] = $this->csvFile->getcsv();
        }
        // finally return all rows
        return $rows;
    }

}