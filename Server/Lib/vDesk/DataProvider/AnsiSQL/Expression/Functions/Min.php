<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression\Functions;

/**
 * SQL aggregate function "MIN()".
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Min extends Distinct {
    
    /**
     * The name of the function.
     */
    protected const Name = "MIN";
    
}