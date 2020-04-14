<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression\Functions;

use vDesk\DataProvider\Expression\IAggregateFunction;

/**
 * Class GroupConcat represents ...
 *
 * @package vDesk\DataProvider\Expression\Functions\MariaDB
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class GroupConcat implements IAggregateFunction {

    /**
     * The name of the function.
     */
    protected const Name = "SUM";

    /**
     * Initializes a new instance of the GroupConcat class.
     *
     * @param string ...$Values
     */
    public function __construct(string ...$Values) {
    }

    /**
     * Returns the string representation of the IAggregateFunction.
     *
     * @return string The string representation of the IAggregateFunction.
     */
    public function __toString() {
        // TODO: Implement __toString() method.
    }
}