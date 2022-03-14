<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression\Functions;

use vDesk\DataProvider\Expression\IAggregateFunction;

/**
 * SQL aggregate function "NOW()".
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Now implements IAggregateFunction {
    
    /**
     * Returns the string representation of the Distinct.
     *
     * @return string The string representation of the Distinct.
     */
    public function __toString(): string {
        return "NOW()";
    }
    
}