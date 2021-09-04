<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL;

use vDesk\DataProvider\MsSQL\Expression\Alter;
use vDesk\DataProvider\MsSQL\Expression\Create;
use vDesk\DataProvider\MsSQL\Expression\Delete;
use vDesk\DataProvider\MsSQL\Expression\Drop;
use vDesk\DataProvider\MsSQL\Expression\Insert;
use vDesk\DataProvider\MsSQL\Expression\Select;
use vDesk\DataProvider\MsSQL\Expression\Update;

/**
 * Facade that provides factory methods to create MsSQL compatible Expressions
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Expression extends \vDesk\DataProvider\AnsiSQL\Expression {

    /**
     * Factory method that creates a new instance of the ISelect class according the configured DataProvider.
     *
     * @param mixed ...$Fields
     *
     * @return \vDesk\DataProvider\MsSQL\Expression\Select
     */
    public static function Select(...$Fields): Select {
        return new Select(...$Fields);
    }

    /**
     * Factory method that creates a new instance of the IInsert class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\MsSQL\Expression\Insert
     */
    public static function Insert(): Insert {
        return new Insert();
    }

    /**
     * Factory method that creates a new instance of the IUpdate class according the configured DataProvider.
     *
     * @param string $Table The name of the table tu update.
     *
     * @return \vDesk\DataProvider\MsSQL\Expression\Update
     */
    public static function Update(string $Table): Update {
        return new Update($Table);
    }

    /**
     * Factory method that creates a new instance of the IDelete class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\MsSQL\Expression\Delete
     */
    public static function Delete(): Delete {
        return new Delete();
    }

    /**
     * Factory method that creates a new instance of the ICreate class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\MsSQL\Expression\Create
     */
    public static function Create(): Create {
        return new Create();
    }

    /**
     * Factory method that creates a new instance of the IAlter class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\MsSQL\Expression\Alter
     */
    public static function Alter(): Alter {
        return new Alter();
    }

    /**
     * Factory method that creates a new instance of the IDrop class according the configured DataProvider.
     *
     * @return \vDesk\DataProvider\MsSQL\Expression\Drop
     */
    public static function Drop(): Drop {
        return new Drop();
    }


}