<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression\Functions;

use vDesk\DataProvider\Expression\IAggregateFunction;

/**
 * SQL function 'NOW()'.
 *
 * @package vDesk\DataProvider\Expression\Functions\MariaDB
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
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