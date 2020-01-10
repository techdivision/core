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
 * Class CsvFileOutputAdapter
 * @package TechDivision\Core\Adapter
 */
class CsvFileOutputAdapter implements OutputAdapterInterface
{
    /**
     * @var CsvFileInterface
     */
    protected $csvFile;

    /**
     * CsvFileOutputAdapter constructor.
     * @param string $csvFilename
     * @param string $csvSeparator
     */
    public function __construct(string $csvFilename, $csvSeparator = ";")
    {
        $this->csvFile = new SimpleCsvFile($csvFilename, $csvSeparator, "w+");
    }

    /**
     * Initialise adapter
     */
    public function init(): void
    {
        $this->csvFile->open();
    }
    
    /**
     * @param array $data
     * @return mixed|void
     */
    public function setData(array $data)
    {
        // check if first line
        if ($this->csvFile->tell() === 0) {
            // write headers first
            $this->csvFile->putcsv(array_keys($data));
        }
        $this->csvFile->putcsv($data);
    }
}
