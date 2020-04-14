<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression\Functions;

/**
 * SQL function 'MIN()'.
 *
 * @package vDesk\DataProvider\Expression\Functions\MariaDB
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Min extends Distinct {

    /**
     * The name of the function.
     */
    protected const Name = "MIN";

}