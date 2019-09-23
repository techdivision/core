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
 * Class CsvFileOutputAdapter
 * @package TechDivision\Core\Adapter
 */
class CsvFileOutputAdapter implements OutputAdapterInterface
{
    protected $data;

    protected $headers = [];

    protected $handle;

    protected $separator;
    /**
     * CsvFileOutputAdapter constructor.
     * @param string $csvFilename
     * @param string $separator
     */
    public function __construct(string $fileName, string $separator)
    {
        $this->handle = $fp = fopen($fileName, 'w');
        $this->separator = $separator;
    }

    /**
     * @param array $data
     * @return mixed|void
     */
    public function setData(array $data)
    {
        if (empty($this->headers)) {
            $this->headers = array_keys($data);
        }
        $this->data[] = $data;
    }

    public function writeCsv()
    {
        fputcsv($this->handle, $this->headers);
        foreach ($this->data as $row) {
            fputcsv($this->handle, $row);
        }
    }
}