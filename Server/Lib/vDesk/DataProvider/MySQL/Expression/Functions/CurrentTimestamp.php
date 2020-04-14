<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression\Functions;

use vDesk\DataProvider\Expression\IAggregateFunction;

/**
 * Class CurrentTimestamp represents ...
 *
 * @package vDesk\DataProvider\Expression\Functions\MariaDB
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class CurrentTimestamp implements IAggregateFunction {
    /**
     * Returns the string representation of the Distinct.
     *
     * @return string The string representation of the Distinct.
     */
    public function __toString() {
        return "CURRENT_TIMESTAMP()";
    }
}