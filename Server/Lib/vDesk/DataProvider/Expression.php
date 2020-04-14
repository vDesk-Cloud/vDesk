<?php
declare(strict_types=1);

namespace vDesk\DataProvider;

use vDesk\DataProvider\Expression\Functions;
use vDesk\DataProvider\Expression\IAggregateFunction;
use vDesk\DataProvider\Expression\ICreate;
use vDesk\DataProvider\Expression\IAlter;
use vDesk\DataProvider\Expression\IDrop;
use vDesk\DataProvider\Expression\IDelete;
use vDesk\DataProvider\Expression\IInsert;
use vDesk\DataProvider\Expression\ISelect;
use vDesk\DataProvider\Expression\IUpdate;
use vDesk\Struct\StaticSingleton;

/**
 * Class Expression represents ...
 *
 * @package vDesk\DataProvider
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Expression extends StaticSingleton {

    /**
     * The current expression provider of the Expression.
     *
     * @var string
     */
    private static string $Provider;

    /**
     * Initializes a new instance of the Expression class.
     *
     * @param string $Provider Initializes the Expression with the specified expression provider.
     */
    public static function _construct(string $Provider = "") {
        self::$Provider = "vDesk\\DataProvider\\{$Provider}\\Expression";
    }
    
    /**
     * Factory method that creates a new instance of the ISelect class according the configured DataProvider.
     *
     * @param mixed ...$Fields
     *
     * @return \vDesk\DataProvider\Expression\ISelect
     */
    public static function Select(...$Fields): ISelect {
        return self::$Provider::Select(...$Fields);
    }
    
    /**
     * Factory method that creates a new instance of the IInsert class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\Expression\IInsert
     */
    public static function Insert(): IInsert {
        return self::$Provider::Insert();
    }
    
    /**
     * Factory method that creates a new instance of the IUpdate class according the configured DataProvider.
     *
     * @param string $Table The name of the table tu update.
     *
     * @return \vDesk\DataProvider\Expression\IUpdate
     */
    public static function Update(string $Table): IUpdate {
        return self::$Provider::Update($Table);
    }
    
    /**
     * Factory method that creates a new instance of the IDelete class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\Expression\IDelete
     */
    public static function Delete(): IDelete {
        return self::$Provider::Delete();
    }
    
    /**
     * Factory method that creates a new instance of the ICreate class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\Expression\ICreate
     */
    public static function Create(): ICreate {
        return self::$Provider::Create();
    }
    
    /**
     * Factory method that creates a new instance of the IAlter class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\Expression\IAlter
     */
    public static function Alter(): IAlter {
        return self::$Provider::Alter();
    }
    
    /**
     * Factory method that creates a new instance of the IDrop class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\Expression\IDrop
     */
    public static function Drop(): IDrop {
        return self::$Provider::Drop();
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
        return Functions::__callStatic($Function, $Arguments);
    }

}