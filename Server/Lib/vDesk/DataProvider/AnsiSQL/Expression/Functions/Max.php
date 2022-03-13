<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression\Functions;

/**
 * SQL aggregate function "MAX()".
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Max extends Distinct {
    
    /**
     * The name of the function.
     */
    protected const Name = "MAX";
    
}