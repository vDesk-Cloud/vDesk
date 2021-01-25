<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression\Functions;

/**
 * SQL function 'SUM()'.
 *
 * @package vDesk\DataProvider\Expression\Functions\MariaDB
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Sum extends Distinct {
    
    /**
     * The name of the function.
     */
    protected const Name = "SUM";
    
}