<?php
declare(strict_types=1);

namespace vDesk\DataProvider\Expression;

/**
 * Interface IAggregateFunction that represents a ...
 *
 * @package vDesk\DataProvider\Expression
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
interface IAggregateFunction {

    /**
     * Returns the string representation of the IAggregateFunction.
     *
     * @return string The string representation of the IAggregateFunction.
     */
    public function __toString();

}