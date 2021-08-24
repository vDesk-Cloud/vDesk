<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL;

use vDesk\Data\IModel;
use vDesk\DataProvider\AnsiSQL\Provider;
use vDesk\DataProvider\Expression\IAggregateFunction;
use vDesk\DataProvider\PgSQL\Expression\Alter;
use vDesk\DataProvider\PgSQL\Expression\Create;
use vDesk\DataProvider\PgSQL\Expression\Delete;
use vDesk\DataProvider\PgSQL\Expression\Drop;
use vDesk\DataProvider\PgSQL\Expression\Insert;
use vDesk\DataProvider\PgSQL\Expression\Select;
use vDesk\DataProvider\PgSQL\Expression\Update;
use vDesk\DataProvider;

/**
 * Class Expression represents ...
 *
 * @package vDesk\DataProvider\PgSQL
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Expression extends DataProvider\AnsiSQL\Expression {

    /**
     * Factory method that creates a new instance of the ISelect class according the configured DataProvider.
     *
     * @param mixed ...$Fields
     *
     * @return \vDesk\DataProvider\PgSQL\Expression\Select
     */
    public static function Select(...$Fields): Select {
        return new Select(...$Fields);
    }

    /**
     * Factory method that creates a new instance of the IInsert class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\PgSQL\Expression\Insert
     */
    public static function Insert(): Insert {
        return new Insert();
    }

    /**
     * Factory method that creates a new instance of the IUpdate class according the configured DataProvider.
     *
     * @param string $Table The name of the table tu update.
     *
     * @return \vDesk\DataProvider\PgSQL\Expression\Update
     */
    public static function Update(string $Table): Update {
        return new Update($Table);
    }

    /**
     * Factory method that creates a new instance of the IDelete class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\PgSQL\Expression\Delete
     */
    public static function Delete(): Delete {
        return new Delete();
    }

    /**
     * Factory method that creates a new instance of the ICreate class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\PgSQL\Expression\Create
     */
    public static function Create(): Create {
        return new Create();
    }

    /**
     * Factory method that creates a new instance of the IAlter class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\PgSQL\Expression\Alter
     */
    public static function Alter(): Alter {
        return new Alter();
    }

    /**
     * Factory method that creates a new instance of the IDrop class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\PgSQL\Expression\Drop
     */
    public static function Drop(): Drop {
        return new Drop();
        $Sanitize     = static fn($Value) => (\is_string($Value) && (int)\preg_match(Provider::SeparatorExpression, $Value, $Matches) > 0)
            ? DataProvider::EscapeField($Matches[1]) . Provider::Separator . DataProvider::EscapeField($Matches[2])
            : DataProvider::Sanitize($Value);
    }


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
        $Matches = [];
        $Sanitize     = static fn($Value) => (\is_string($Value) && (int)\preg_match(Provider::SeparatorExpression, $Value, $Matches) > 0)
            ? DataProvider::EscapeField($Matches[1]) . Provider::Separator . DataProvider::EscapeField($Matches[2])
            : DataProvider::Sanitize($Value);

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