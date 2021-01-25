<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL;

use vDesk\DataProvider\Expression\IAggregateFunction;
use vDesk\DataProvider\MySQL\Expression\Alter;
use vDesk\DataProvider\MySQL\Expression\Create;
use vDesk\DataProvider\MySQL\Expression\Delete;
use vDesk\DataProvider\MySQL\Expression\Drop;
use vDesk\DataProvider\MySQL\Expression\Insert;
use vDesk\DataProvider\MySQL\Expression\Select;
use vDesk\DataProvider\MySQL\Expression\Update;
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
     * Factory method that creates a new instance of the ISelect class according the configured DataProvider.
     *
     * @param mixed ...$Fields
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Select
     */
    public static function Select(...$Fields): Select {
        return new Select(...$Fields);
    }
    
    /**
     * Factory method that creates a new instance of the IInsert class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Insert
     */
    public static function Insert(): Insert {
        return new Insert();
    }
    
    /**
     * Factory method that creates a new instance of the IUpdate class according the configured DataProvider.
     *
     * @param string $Table The name of the table tu update.
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Update
     */
    public static function Update(string $Table): Update {
        return new Update($Table);
    }
    
    /**
     * Factory method that creates a new instance of the IDelete class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Delete
     */
    public static function Delete(): Delete {
        return new Delete();
    }
    
    /**
     * Factory method that creates a new instance of the ICreate class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Create
     */
    public static function Create(): Create {
        return new Create();
    }
    
    /**
     * Factory method that creates a new instance of the IAlter class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Alter
     */
    public static function Alter(): Alter {
        return new Alter();
    }
    
    /**
     * Factory method that creates a new instance of the IDrop class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Drop
     */
    public static function Drop(): Drop {
        return new Drop();
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
        
        $Sanitize = static function($Value) use ($Aliases) {
            //Check if the value is a referenced column.
            if(\is_string($Value)) {
                foreach($Aliases as $Alias) {
                    if(\strpos(\trim($Value), "{$Alias}.") !== false) {
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
                    switch((string)\key($Value)) {
                        //Check if a nested statement has been passed.
                        case "0":
                            $AndStatements[] = "({$Field} = " . \implode(" OR {$Field} = ", \array_map($Sanitize, $Value)) . ")";
                            continue 2;
                        //Check if an "IN"-Statement has been passed.
                        case self::In:
                            $AndStatements[] = "{$Field} IN (" . \implode(",", \array_map($Sanitize, $Value[self::In])) . ")";
                            continue 2;
                        //Check if an "NOT IN"-Statement has been passed.
                        case self::NotIn:
                            $AndStatements[] = "{$Field} NOT IN (" . \implode(",", \array_map($Sanitize, $Value[self::NotIn])) . ")";
                            continue 2;
                        //Check if an "LIKE"-Statement has been passed.
                        case self::Like:
                            $AndStatements[] = "{$Field} LIKE '{$Value[self::Like]}'";
                            continue 2;
                        //Check if an "BETWEEN"-Statement has been passed.
                        case self::Between:
                            $AndStatements[] = "({$Field} BETWEEN {$Sanitize($Value[self::Between][0])} AND {$Sanitize($Value[self::Between][1])})";
                            continue 2;
                        //Check if an "NOT BETWEEN"-Statement has been passed.
                        case self::NotBetween:
                            $AndStatements[] = "({$Field} NOT BETWEEN {$Sanitize($Value[self::NotBetween][0])} AND {$Sanitize($Value[self::NotBetween][1])})";
                            continue 2;
                        case self::Regex:
                            $AndStatements[] = "{$Field} REGEXP '{$Value[self::Regex]}'";
                            continue 2;
                        case self::NotRegex:
                            $AndStatements[] = "{$Field} NOT REGEXP '{$Value[self::NotRegex]}'";
                            continue 2;
                        //Use the key of the statement as a normal operator.
                        default:
                            $AndStatements[] = "{$Field} " . \key($Value) . " " . $Sanitize(\current($Value));
                            continue 2;
                    }
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