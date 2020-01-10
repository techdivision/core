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
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.techdivision.com
 */

declare(strict_types=1);

namespace TechDivision\Core\Adapter;

/**
 * Class AdapterInterface
 * @package TechDivision\Core\Adapter
 */
interface AdapterInterface
{
    /**
     * Initialise adapter
     * @return void
     */
    public function init(): void;
}