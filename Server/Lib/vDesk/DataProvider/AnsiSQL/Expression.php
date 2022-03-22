<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL;

use vDesk\Data\IModel;
use vDesk\DataProvider;
use vDesk\DataProvider\Expression as Where;
use vDesk\DataProvider\Expression\IAggregateFunction;

/**
 * Utility class for AnsiSQL compatible Expressions.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Expression {

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
                    if(\str_contains(\trim($Value), $Alias . DataProvider::$Separator)) {
                        return DataProvider::SanitizeField($Value);
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
                    $Field           = DataProvider::SanitizeField($Field);
                    $AndStatements[] = match (\key($Value)) {
                        0 => "({$Field} = " . \implode(" OR {$Field} = ", \array_map($Sanitize, $Value)) . ")",
                        Where::In => "{$Field} " . Where::In . " (" . \implode(",", \array_map($Sanitize, $Value[Where::In])) . ")",
                        Where::NotIn => "{$Field} " . Where::NotIn . " (" . \implode(",", \array_map($Sanitize, $Value[Where::NotIn])) . ")",
                        Where::Like => "{$Field} " . Where::Like . " '{$Value[Where::Like]}'",
                        Where::Between => "({$Field} " . Where::Between . " {$Sanitize($Value[Where::Between][0])} AND {$Sanitize($Value[Where::Between][1])})",
                        Where::NotBetween => "({$Field} " . Where::NotBetween . " {$Sanitize($Value[Where::NotBetween][0])} AND {$Sanitize($Value[Where::NotBetween][1])})",
                        Where::Regex => "{$Field} " . Where::Regex . " '{$Value[Where::Regex]}'",
                        Where::NotRegex => "{$Field} " . Where::NotRegex . " '{$Value[Where::NotRegex]}'",
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