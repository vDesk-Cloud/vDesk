<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL;

use vDesk\DataProvider\Expression\IAggregateFunction;
use vDesk\DataProvider;
use vDesk\Data\IModel;

/**
 * Class Expression represents ...
 *
 * @package vDesk\DataProvider\Expression
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
abstract class Expression {
    
    /**
     *
     */
    public const In = "IN";
    
    /**
     *
     */
    public const NotIn = "NOT IN";
    
    /**
     *
     */
    public const Like = "LIKE";
    
    /**
     *
     */
    public const Between = "BETWEEN";
    
    /**
     *
     */
    public const NotBetween = "NOT BETWEEN";
    
    /**
     *
     */
    public const Regex = "REGEXP";
    
    /**
     *
     */
    public const NotRegex = "NOT REGEXP";

    /**
     * Transforms sets of specified conditions into a SQL-conform format string.
     *
     * @param string[] $Aliases The aliases of the conditions.
     * @param array    ...$Conditions
     *
     * @return string A string containing the specified conditions in a SQL-conform format.
     */
    public static function TransformConditions(array $Aliases = [], array ...$Conditions): string {
        
        $OrStatements = [];
        
        $Sanitize = static function($Value) use ($Aliases) {
            //Check if the value is a referenced column.
            if(\is_string($Value)) {
                foreach($Aliases as $Alias) {
                    if(\str_contains(\trim($Value), "{$Alias}.")) {
                        return $Value;
                    }
                }
            }
            return DataProvider::Sanitize($Value);
        };
        
        foreach($Conditions as $Condition) {
            $AndStatements = [];
            foreach($Condition as $Field => $Value) {
                if(\is_array($Value)) {
                    //Check if a set of nested statements has been passed.
                    if(\is_int($Field)) {
                        $AndStatements[] = self::TransformConditions($Aliases, ...$Value);
                        continue;
                    }
                    $Field = DataProvider::SanitizeField($Field);
                    $AndStatements[] = match (\key($Value)) {
                        0 => "({$Field} = " . \implode(" OR {$Field} = ", \array_map($Sanitize, $Value)) . ")",
                        self::In => "{$Field} IN (" . \implode(",", \array_map($Sanitize, $Value[self::In])) . ")",
                        self::NotIn => "{$Field} NOT IN (" . \implode(",", \array_map($Sanitize, $Value[self::NotIn])) . ")",
                        self::Like => "{$Field} LIKE '{$Value[self::Like]}'",
                        self::Between => "({$Field} BETWEEN {$Sanitize($Value[self::Between][0])} AND {$Sanitize($Value[self::Between][1])})",
                        self::NotBetween => "({$Field} NOT BETWEEN {$Sanitize($Value[self::NotBetween][0])} AND {$Sanitize($Value[self::NotBetween][1])})",
                        self::Regex => "{$Field} REGEXP '{$Value[self::Regex]}'",
                        self::NotRegex => "{$Field} NOT REGEXP '{$Value[self::NotRegex]}'",
                        default => "{$Field} " . \key($Value) . " " . $Sanitize(\current($Value))
                    };
                    continue;
                }
                $Field = DataProvider::SanitizeField($Field);
                if($Value instanceof IAggregateFunction) {
                    $AndStatements[] = "{$Field} = {$Value}";
                    continue;
                }
                if($Value instanceof IModel) {
                    $AndStatements[] = "{$Field} = {$Value->ID()}";
                    continue;
                }
                $AndStatements[] = "{$Field} = {$Sanitize($Value)}";
            }
            $OrStatements[] = \count($AndStatements) > 1
                ? "(" . \implode(" AND ", $AndStatements) . ")"
                : \implode(" AND ", $AndStatements);
        }
        
        return \count($OrStatements) > 1
            ? "(" . \implode(" OR ", $OrStatements) . ")"
            : \implode(" OR ", $OrStatements);
    }
    
}