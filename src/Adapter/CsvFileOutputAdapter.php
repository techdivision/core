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

use TechDivision\Core\File\CsvFileInterface;
use TechDivision\Core\File\SimpleCsvFile;

/**
 * CSV file output adapter implementation.
 *
 * @author    Johann Zelger <j.zelger@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.techdivision.com
 */
class CsvFileOutputAdapter implements OutputAdapterInterface
{
    /**
     * Holds a csv file implementation.
     *
     * @var CsvFileInterface
     */
    protected CsvFileInterface $csvFile;

    /**
     * CsvFileOutputAdapter constructor.
     *
     * @param string $csvFilename
     * @param string $csvSeparator
     * @param string $openMode
     */
    public function __construct(string $csvFilename, $csvSeparator = ";", $openMode = "w+")
    {
        $this->csvFile = new SimpleCsvFile($csvFilename, $csvSeparator, $openMode);
    }

    /**
     * Initialise adapter
     *
     * @return void
     */
    public function init(): void
    {
        $this->csvFile->open();
    }
    
    /**
     * Sets data, means writes data directly to csv file.
     *
     * @param array $data
     * @return bool|int
     */
    public function setData(array $data)
    {
        // check if first line
        if ($this->csvFile->tell() === 0) {
            // write headers first
            $this->csvFile->putcsv(array_keys($data));
        }
        return $this->csvFile->putcsv($data);
    }
}
