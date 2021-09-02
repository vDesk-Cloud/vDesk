<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression\Functions;

/**
 * SQL function 'MAX()'.
 *
 * @package vDesk\DataProvider\Expression\Functions\MariaDB
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Max extends Distinct {
    
    /**
     * The name of the function.
     */
    protected const Name = "MAX";
    
}