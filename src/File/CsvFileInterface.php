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
 * Interface CsvFileInterface
 * @package TechDivision\Core\File
 */
interface CsvFileInterface extends FileInterface
{
    /**
     * Gets line from file pointer and parse for CSV fields
     * @param bool $withHeaders
     * @return array|null|false an indexed array containing the fields read.
     */
    public function getcsv($withHeaders = true);

    /**
     * @return array
     */
    public function getHeaders();

    /**
     * @return int
     */
    public function rewindToDataPosition();
}
