<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression\Functions;

use vDesk\DataProvider\Expression\IAggregateFunction;

/**
 * Class CurrentTimestamp represents ...
 *
 * @package vDesk\DataProvider\Expression\Functions\MariaDB
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class CurrentTimestamp implements IAggregateFunction {
    /**
     * Returns the string representation of the Distinct.
     *
     * @return string The string representation of the Distinct.
     */
    public function __toString(): string {
        return "CURRENT_TIMESTAMP()";
    }
}