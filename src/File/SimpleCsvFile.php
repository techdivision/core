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

namespace TechDivision\Core\File;

/**
 * Class SimpleCsvFile
 * @package TechDivision\Core\File
 */
class SimpleCsvFile implements CsvFileInterface, \Iterator
{
    /**
     * @var resource
     */
    protected $fileHandle;

    /**
     * @var string
     */
    protected $separator;

    /**
     * @var int
     */
    protected $dataStartPosition;

    /**
     * @var array
     */
    protected $iteratorCurrentData = [];

    /**
     * @var int
     */
    protected $iteratorKey = 0;

    /**
     * @var int
     */
    protected $iteratorCurrentPosition = 0;

    /**
     * @var int
     */
    protected $iteratorNextPosition = 0;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * SimpleCsvFile constructor.
     * @param $filename
     * @param string $separator
     * @param string $open_mode
     */
    public function __construct($filename, $separator = ";", $open_mode = 'r')
    {
        // set fileHandle resource
        $this->open($filename, $open_mode);
        // set separator
        $this->separator = $separator;
        // read header by default
        $this->getHeaders();
    }

    /**
     * @return array|false
     */
    public function getHeaders()
    {
        // if headers are not set yet
        if (empty($this->headers)) {
            // go to the beginning of the file
            $this->rewind();
            // read first line as csv which are the headers and save them in internal property
            $this->headers = $this->getcsv(false);
            // save file position after headers to get start of data when needed
            $this->dataStartPosition = $this->tell();
        }
        // return the headers
        return $this->headers;
    }

    /**
     * @return int
     */
    public function rewindToDataPosition()
    {
        return $this->seek($this->dataStartPosition);
    }

    /**
     * @param $filename
     * @param $mode
     * @param null $use_include_path
     * @param null $context
     * @return bool
     */
    public function open($filename, $mode, $use_include_path = false)
    {
        return $this->fileHandle = fopen($filename, $mode, $use_include_path);
    }

    /**
     * @return bool
     */
    public function close()
    {
        return fclose($this->fileHandle);
    }

    /**
     * @return array|false|null
     */
    public function getcsv($withHeaders = true)
    {
        $rowData = fgetcsv($this->fileHandle, 0, $this->separator);

        if ($withHeaders === true && $rowData !== false) {
            return array_combine(
                $this->getHeaders() + array_keys($rowData), $rowData
            );
            //return array_combine($this->getHeaders(), $rowData);
        }
        return $rowData;
    }

    /**
     * @param array $row
     * @return bool|int
     */
    public function putcsv(array $row)
    {
        return fputcsv($this->fileHandle, $row, $this->separator);
    }

    /**
     * @return bool|string
     */
    public function gets()
    {
        return fgets($this->fileHandle);
    }

    /**
     * @param $position
     * @return int Upon success, returns 0; otherwise, returns -1. Note that seeking
     */
    public function seek($position)
    {
        return fseek($this->fileHandle, $position);
    }

    /**
     * @return bool
     */
    public function eof()
    {
        return feof($this->fileHandle);
    }

    /**
     * @return bool|int
     */
    public function tell()
    {
        return ftell($this->fileHandle);
    }

    /**
     * Rewind the Iterator to the first element
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        // set key to 0
        $this->iteratorKey = 0;
        // go to the beginning of the file
        return rewind($this->fileHandle);
    }

    /**
     * Return the current element
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->iteratorCurrentData;
    }

    /**
     * Return the key of the current element
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->iteratorKey;
    }

    /**
     * Move forward to next element
     * @return void Any returned value is ignored.
     */
    public function next(): void
    {
        $this->seek($this->iteratorNextPosition);
    }

    /**
     * Checks if current position is valid
     * @return boolean The return value will be casted to boolean and then evaluated.
     */
    public function valid()
    {
        // goto data position if file pointer is at the beginning
        if ($this->tell() === 0) {
            $this->rewindToDataPosition();
        }
        // save current iterator position
        $this->iteratorCurrentPosition = $this->tell();
        // parse current line and combine headers to data row array
        $this->iteratorCurrentData = $this->getcsv();
        // increment key
        $this->iteratorKey++;
        // save next position after line parsing
        $this->iteratorNextPosition = $this->tell();
        // check if current parsed line and end file is still valid
        $valid = $this->eof() !== true || $this->iteratorCurrentData !== false;
        // go back to current position
        $this->seek($this->iteratorCurrentPosition);
        // return validation result
        return $valid;
    }
}

