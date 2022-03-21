<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression\Functions;

use vDesk\DataProvider\Expression\IAggregateFunction;

/**
 * SQL aggregate function "CURRENT_TIMESTAMP()".
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class CurrentTimestamp implements IAggregateFunction {
    /**
     * Returns the string representation of the CurrentTimestamp.
     *
     * @return string The string representation of the CurrentTimestamp.
     */
    public function __toString(): string {
        return "CURRENT_TIMESTAMP()";
    }
}