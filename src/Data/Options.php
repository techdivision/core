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

namespace TechDivision\Core\Data;

/**
 * Class Options
 * @package TechDivision\Core\Data
 */
class Options implements DataObjectInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Options constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->data = $options;
    }

    /**
     * @param string $option
     * @return mixed
     * @throws \Exception
     */
    public function get(string $option = null)
    {
        if ($option === null) {
            return $this->data;
        }
        if (!isset($this->data[$option])) {
            throw new \Exception("No option given for key '$option'");
        }
        return $this->data[$option];
    }

    /**
     * @param string $option
     * @param mixed $value
     */
    public function set(string $option, $value)
    {
        $this->data[$option] = $value;
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->data = [];
    }
}
