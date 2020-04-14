<?php
declare(strict_types=1);

namespace vDesk\DataProvider\Expression;

use vDesk\Struct\StaticSingleton;

/**
 * Class Functions represents ...
 *
 * @package vDesk\DataProvider\Expression
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Functions extends StaticSingleton {

    /**
     * The current function provider of the Functions.
     *
     * @var string
     */
    private static string $Provider;

    /**
     * Initializes a new instance of the Functions class.
     *
     * @param string $Provider Initializes the Functions with the specified function provider.
     */
    public static function _construct(string $Provider = "") {
        self::$Provider = "vDesk\\DataProvider\\{$Provider}\\Expression\\Functions";
    }
    
    /**
     * Factory method that creates a new instance of the IAggregateFunction class according the configured DataProvider.
     *
     * @param string $Function
     * @param array  $Arguments
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction
     */
    public static function __callStatic(string $Function, array $Arguments): IAggregateFunction {
        return self::$Provider::__callStatic($Function, $Arguments);
    }
    
    /**
     * MIN().
     *
     * @param array $Values
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction
     */
    public static function Min(...$Values): IAggregateFunction {
        return self::$Provider::Min(...$Values);
    }
    
    /**
     * MAX().
     *
     * @param array $Values
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction
     */
    public static function Max(...$Values): IAggregateFunction {
        return self::$Provider::Max(...$Values);
    }
    
    /**
     * SUM().
     *
     * @param array $Values
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction
     */
    public static function Sum(...$Values): IAggregateFunction {
        return self::$Provider::Sum(...$Values);
    }
    
    /**
     * COUNT().
     *
     * @param array $Values
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction
     */
    public static function Count(...$Values): IAggregateFunction {
        return self::$Provider::Count(...$Values);
    }
    
    /**
     * NOW().
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction
     */
    public static function Now(): IAggregateFunction {
        return self::$Provider::Now();
    }
    
    /**
     * AVG().
     *
     * @param array $Values
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction
     */
    public static function Avg(...$Values): IAggregateFunction {
        return self::$Provider::Count(...$Values);
    }
    
    /**
     * GROUP_CONCAT().
     *
     * @param array $Values
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction
     */
    public static function GroupConcat(...$Values): IAggregateFunction {
        return self::$Provider::GroupConcat(...$Values);
    }
    
    /**
     * CURRENT_TIMESTAMP().
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction
     */
    public static function CurrentTimestamp(): IAggregateFunction {
        return self::$Provider::CurrentTimestamp();
    }
}