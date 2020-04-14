<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider\Expression\Functions\Generic;
use vDesk\DataProvider\MySQL\Expression\Functions\Avg;
use vDesk\DataProvider\MySQL\Expression\Functions\Count;
use vDesk\DataProvider\MySQL\Expression\Functions\CurrentTimestamp;
use vDesk\DataProvider\MySQL\Expression\Functions\GroupConcat;
use vDesk\DataProvider\MySQL\Expression\Functions\Max;
use vDesk\DataProvider\MySQL\Expression\Functions\Min;
use vDesk\DataProvider\MySQL\Expression\Functions\Now;
use vDesk\DataProvider\MySQL\Expression\Functions\Sum;

/**
 * Class MariaDB represents ...
 *
 * @package vDesk\DataProvider\Expression\Functions
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
abstract class Functions {
    
    /**
     * Factory method that creates a new instance of the IAggregateFunction class according the configured DataProvider.
     *
     * @param string $Function
     * @param array  $Arguments
     *
     * @return \vDesk\DataProvider\Expression\Functions
     */
    public static function __callStatic(string $Function, array $Arguments): Generic {
        return new Generic($Function, ...$Arguments);
    }
    
    /**
     * MIN().
     *
     * @param string $Field
     * @param bool   $Distinct
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Functions\Min
     */
    public static function Min(string $Field, bool $Distinct = false): Min {
        return new Min($Field, $Distinct);
    }
    
    /**
     * MAX().
     *
     * @param string $Field
     * @param bool   $Distinct
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Functions\Max
     */
    public static function Max(string $Field, bool $Distinct = false): Max {
        return new Max($Field, $Distinct);
    }
    
    /**
     * SUM().
     *
     * @param string $Field
     * @param bool   $Distinct
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Functions\Sum
     */
    public static function Sum(string $Field, bool $Distinct = false): Sum {
        return new Sum($Field, $Distinct);
    }
    
    /**
     * COUNT().
     *
     * @param string $Field
     * @param bool   $Distinct
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Functions\Count
     */
    public static function Count(string $Field, bool $Distinct = false): Count {
        return new Count($Field, $Distinct);
    }
    
    /**
     * NOW().
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Functions\Now
     */
    public static function Now(): Now {
        return new Now();
    }
    
    /**
     * AVG().
     *
     * @param string $Field
     * @param bool   $Distinct
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Functions\Avg
     */
    public static function Avg(string $Field, bool $Distinct = false): Avg {
        return new Avg($Field, $Distinct);
    }
    
    /**
     * GROUP_CONCAT().
     *
     * @param array $Values
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Functions\GroupConcat
     */
    public static function GroupConcat(...$Values): GroupConcat {
        return new GroupConcat(...$Values);
    }
    
    /**
     * CURRENT_TIMESTAMP().
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Functions\CurrentTimestamp
     */
    public static function CurrentTimestamp(): CurrentTimestamp {
        return new CurrentTimestamp();
    }
}