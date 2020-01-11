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
 * Interface for generic data objects.
 *
 * @author    Johann Zelger <j.zelger@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.techdivision.com
 */
interface DataObjectInterface
{
    /**
     * Gets a value by given option.
     * If no options i given, all available options are return
     *
     * @param string $option
     * @return mixed
     */
    public function get(string $option = null);

    /**
     * Sets a value for given option.
     *
     * @param string $option
     * @param mixed $value
     */
    public function set(string $option, $value);

    /**
     * Returns whether the given options exists or not.
     *
     * @param string $option
     * @return mixed
     */
    public function has(string $option);

    /**
     * Resets the objects and its data.
     *
     * @return void
     */
    public function reset();
}
