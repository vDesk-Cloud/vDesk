<?php
declare(strict_types=1);

namespace vDesk\DataProvider\Expression;

use vDesk\DataProvider\IExpression;

/**
 * Interface that represents a DROP SQL expression.
 *
 * @package vDesk\DataProvider\Expression
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
interface IDrop extends IExpression {
    
    /**
     * Creates a table.
     *
     * @param string $Name The name of the table to drop.
     *
     * @return \vDesk\DataProvider\Expression\IDrop The current instance for further chaining.
     */
    public function Table(string $Name): IDrop;
    
    /**
     * Creates a database.
     *
     * @param string $Name The name of the database to drop.
     *
     * @return \vDesk\DataProvider\Expression\IDrop The current instance for further chaining.
     */
    public function Database(string $Name): IDrop;

}