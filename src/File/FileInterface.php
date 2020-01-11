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
 * Interface for file.
 *
 * @author    Johann Zelger <j.zelger@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.techdivision.com
 */
interface FileInterface
{
    /**
     * Opens the file.
     *
     * @return mixed
     */
    public function open();

    /**
     * Closes an open file pointer.
     *
     * @return bool true on success or false on failure.
     */
    public function close();

    /**
     * Gets line from file pointer.
     *
     * @return bool|string a string of up to length - 1 bytes read from
     */
    public function gets();

    /**
     * Seeks on a file pointer.
     *
     * @param $offset
     * @return int Upon success, returns 0; otherwise, returns -1. Note that seeking
     */
    public function seek($offset);

    /**
     * Tests for end-of-file on a file pointer.
     *
     * @return bool true if the file pointer is at EOF or an error occurs
     */
    public function eof();

    /**
     * Returns the current position of the file read/write pointer.
     *
     * @return bool true on success or false on failure.
     */
    public function tell();
}
