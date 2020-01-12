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
 * Simple csv file implementation as iterator for being able to stream data line by line while iterations.
 *
 * @author    Johann Zelger <j.zelger@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.techdivision.com
 */
class SimpleCsvFile implements CsvFileInterface, \Iterator
{
    /**
     * The filename including the path relative or absolute.
     *
     * @var string
     */
    protected string $filename;

    /**
     * The file pointer as php resource.
     *
     * @var resource
     */
    protected $fileHandle;

    /**
     * The separator to use for csv parsing.
     *
     * @var string
     */
    protected string $separator;

    /**
     * How to open the file pointer.
     *
     * @var string
     */
    protected string $open_mode;

    /**
     * If include path should be used when handling filename without path.
     *
     * @var bool
     */
    protected bool $use_include_path;

    /**
     * The file position where the data starts.
     *
     * @var int
     */
    protected int $dataStartPosition;

    /**
     * Current parsed data in iteration process.
     *
     * @var mixed
     */
    protected $iteratorCurrentData = [];

    /**
     * Current iterator key.
     *
     * @var int
     */
    protected int $iteratorKey = 0;

    /**
     * Current file position in iteration process.
     *
     * @var int
     */
    protected int $iteratorCurrentPosition = 0;

    /**
     * The file position to the next row.
     *
     * @var int
     */
    protected int $iteratorNextPosition = 0;

    /**
     * The parsed headers.
     *
     * @var mixed
     */
    protected $headers = [];

    /**
     * SimpleCsvFile constructor.
     *
     * @param $filename
     * @param string $separator
     * @param string $open_mode
     * @param bool $use_include_path
     */
    public function __construct(
        string $filename,
        string $separator = ";",
        string $open_mode = 'r',
        bool $use_include_path = false
    ) {
        $this->filename = $filename;
        $this->separator = $separator;
        $this->open_mode = $open_mode;
        $this->use_include_path = $use_include_path;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function rewindToDataPosition(): int
    {
        return $this->seek($this->dataStartPosition);
    }

    /**
     * {@inheritDoc}
     */
    public function open()
    {
        $this->fileHandle = fopen(
            $this->filename,
            $this->open_mode,
            $this->use_include_path
        );
        // read header by default
        $this->getHeaders();
        return $this->fileHandle;
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        return fclose($this->fileHandle);
    }

    /**
     * {@inheritDoc}
     */
    public function getcsv(bool $withHeaders = true)
    {
        $rowData = fgetcsv($this->fileHandle, 0, $this->separator);

        if ($withHeaders === true && $rowData !== false) {
            // Returns tolerant key value combined array even if column count does not match headers count.
            return array_combine(
                $this->getHeaders() + array_keys($rowData),
                $rowData
            );
            // An intolerant implementation of combining data to keys would be:
            // return array_combine($this->getHeaders(), $rowData);
        }

        return $rowData;
    }

    /**
     * {@inheritDoc}
     */
    public function putcsv(array $row)
    {
        return fputcsv($this->fileHandle, $row, $this->separator);
    }

    /**
     * {@inheritDoc}
     */
    public function gets()
    {
        return fgets($this->fileHandle);
    }

    /**
     * {@inheritDoc}
     */
    public function seek($position)
    {
        return fseek($this->fileHandle, $position);
    }

    /**
     * {@inheritDoc}
     */
    public function eof()
    {
        return feof($this->fileHandle);
    }

    /**
     * {@inheritDoc}
     */
    public function tell()
    {
        return ftell($this->fileHandle);
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        // set key to 0
        $this->iteratorKey = 0;
        // go to the beginning of the file
        return rewind($this->fileHandle);
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return $this->iteratorCurrentData;
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return $this->iteratorKey;
    }

    /**
     * {@inheritDoc}
     */
    public function next(): void
    {
        $this->seek($this->iteratorNextPosition);
    }

    /**
     * {@inheritDoc}
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
