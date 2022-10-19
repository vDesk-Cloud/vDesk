<?php
declare(strict_types=1);

namespace vDesk\DataProvider\Expression;

/**
 * Interface for SQL aggregate functions.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IAggregateFunction {

    /**
     * Returns the string representation of the IAggregateFunction.
     *
     * @return string The string representation of the IAggregateFunction.
     */
    public function __toString(): string;

}