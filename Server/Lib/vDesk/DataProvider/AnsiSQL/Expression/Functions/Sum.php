<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression\Functions;

/**
 * SQL aggregate function "SUM()".
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Sum extends Distinct {
    
    /**
     * The name of the function.
     */
    protected const Name = "SUM";
    
}