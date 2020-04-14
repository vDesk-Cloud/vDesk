<?php
declare(strict_types=1);

namespace vDesk\DataProvider\Expression;

use vDesk\DataProvider\IExpression;

/**
 * Interface that represents a CREATE SQL expression.
 *
 * @package vDesk\DataProvider\Expression
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
interface ICreate extends IExpression {
    
    /**
     * Creates a table.
     *
     * @param string  $Name    The name of the table.
     * @param array[] $Fields  The fields of the table.
     * @param array[] $Indexes The indexes of the table.
     * @param array   $Options The DataProvider specific options of the table.
     *
     * @return \vDesk\DataProvider\Expression\ICreate The current instance for further chaining.
     */
    public function Table(string $Name, array $Fields = [], array $Indexes = [], array $Options = []): ICreate;
    
    /**
     * Creates a database.
     *
     * @param string $Name The name of the database to create.
     *
     * @return \vDesk\DataProvider\Expression\ICreate The current instance for further chaining.
     */
    public function Database(string $Name): ICreate;

}