<?php
declare(strict_types=1);

namespace vDesk\DataProvider\Expression;

use vDesk\Configuration\Settings;

/**
 * Facade that provides factory methods for creating aggregate functions compatible with the current configured DataProvider.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Functions {

    /**
     * The current function provider of the Functions.
     *
     * @var string
     */
    private static string $Provider = "";

    /**
     * Initializes a new instance of the Functions class.
     *
     * @param string $Provider Initializes the Functions with the specified function provider.
     */
    public function __construct(string $Provider) {
        self::$Provider = "vDesk\\DataProvider\\{$Provider}\\Expression\\Functions";
    }

    /**
     * Factory method that creates a new instance of the IAggregateFunction class according the configured DataProvider.
     *
     * @param string $Function  The name of the function to create.
     * @param array  $Arguments The arguments of the function to apply.
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction A new instance of the IAggregateFunction-implementation of the current configured DataProvider.
     */
    public static function __callStatic(string $Function, array $Arguments): IAggregateFunction {
        return self::$Provider::__callStatic($Function, $Arguments);
    }

    /**
     * Factory method that creates new "MIN()"-IAggregateFunction according the current configured DataProvider.
     *
     * @param string $Field    The field to get the lowest value of.
     * @param bool   $Distinct Flag indicating whether to prepend a "DISTINCT"-statement.
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction A new instance of the IAggregateFunction-implementation of the current configured DataProvider.
     */
    public static function Min(string $Field, bool $Distinct = false): IAggregateFunction {
        return self::$Provider::Min($Field, $Distinct);
    }

    /**
     * Factory method that creates new "MAX()"-IAggregateFunction according the current configured DataProvider.
     *
     * @param string $Field    The field to get the highest value of.
     * @param bool   $Distinct Flag indicating whether to prepend a "DISTINCT"-statement.
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction A new instance of the IAggregateFunction-implementation of the current configured DataProvider.
     */
    public static function Max(string $Field, bool $Distinct = false): IAggregateFunction {
        return self::$Provider::Max($Field, $Distinct);
    }

    /**
     * Factory method that creates new "SUM()"-IAggregateFunction according the current configured DataProvider.
     *
     * @param string $Field    The field to sum the values of.
     * @param bool   $Distinct Flag indicating whether to prepend a "DISTINCT"-statement.
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction A new instance of the IAggregateFunction-implementation of the current configured DataProvider.
     */
    public static function Sum(string $Field, bool $Distinct = false): IAggregateFunction {
        return self::$Provider::Sum($Field, $Distinct);
    }

    /**
     * Factory method that creates new "COUNT()"-IAggregateFunction according the current configured DataProvider.
     *
     * @param string $Field    The field to count the rows of.
     * @param bool   $Distinct Flag indicating whether to prepend a "DISTINCT"-statement.
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction A new instance of the IAggregateFunction-implementation of the current configured DataProvider.
     */
    public static function Count(string $Field, bool $Distinct = false): IAggregateFunction {
        return self::$Provider::Count($Field, $Distinct);
    }

    /**
     * Factory method that creates new "NOW()"-IAggregateFunction according the current configured DataProvider.
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction A new instance of the IAggregateFunction-implementation of the current configured DataProvider.
     */
    public static function Now(): IAggregateFunction {
        return self::$Provider::Now();
    }

    /**
     * Factory method that creates new "AVG()"-IAggregateFunction according the current configured DataProvider.
     *
     * @param string $Field    The field to get the average value of.
     * @param bool   $Distinct Flag indicating whether to prepend a "DISTINCT"-statement.
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction A new instance of the IAggregateFunction-implementation of the current configured DataProvider.
     */
    public static function Avg(string $Field, bool $Distinct = false): IAggregateFunction {
        return self::$Provider::Avg($Field, $Distinct);
    }

    /**
     * Factory method that creates new "GROUP_CONCAT()/GROUPING()"-IAggregateFunction according the current configured DataProvider.
     *
     * @param array $Values
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction A new instance of the IAggregateFunction-implementation of the current configured DataProvider.
     */
    public static function Group(...$Values): IAggregateFunction {
        return self::$Provider::Group(...$Values);
    }

    /**
     * Factory method that creates new "CURRENT_TIMESTAMP()"-IAggregateFunction according the current configured DataProvider.
     *
     * @return \vDesk\DataProvider\Expression\IAggregateFunction A new instance of the IAggregateFunction-implementation of the current configured DataProvider.
     */
    public static function CurrentTimestamp(): IAggregateFunction {
        return self::$Provider::CurrentTimestamp();
    }
}

//Initialize Functions.
if(Settings::$Local["DataProvider"]->Count > 0) {
    new Functions(Settings::$Local["DataProvider"]["Provider"]);
}