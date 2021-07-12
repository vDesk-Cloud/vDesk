<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL;

use vDesk\DataProvider\MySQL\Expression\Alter;
use vDesk\DataProvider\MySQL\Expression\Create;
use vDesk\DataProvider\MySQL\Expression\Delete;
use vDesk\DataProvider\MySQL\Expression\Drop;
use vDesk\DataProvider\MySQL\Expression\Insert;
use vDesk\DataProvider\MySQL\Expression\Select;
use vDesk\DataProvider\MySQL\Expression\Update;

/**
 * Class Expression represents ...
 *
 * @package vDesk\DataProvider\Expression
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
abstract class Expression extends \vDesk\DataProvider\AnsiSQL\Expression {
    
    /**
     * Factory method that creates a new instance of the ISelect class according the configured DataProvider.
     *
     * @param mixed ...$Fields The fields to select.
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
    
}