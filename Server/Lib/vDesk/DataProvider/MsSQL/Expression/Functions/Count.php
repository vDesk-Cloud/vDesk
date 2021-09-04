<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression\Functions;

/**
 * SQL function 'COUNT()'.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Count extends Distinct {
    
    /**
     * The name of the function.
     */
    protected const Name = "COUNT";
    
}