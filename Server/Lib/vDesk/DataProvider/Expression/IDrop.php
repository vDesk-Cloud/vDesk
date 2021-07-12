<?php
declare(strict_types=1);

namespace vDesk\DataProvider\Expression;

use vDesk\DataProvider\IExpression;

/**
 * Interface that represents a DROP SQL expression.
 *
 * @package vDesk\DataProvider\Expression
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IDrop extends IExpression {

    /**
     * Drops a table.
     *
     * @param string $Name The name of the table to drop.
     *
     * @return \vDesk\DataProvider\Expression\IDrop The current instance for further chaining.
     */
    public function Table(string $Name): IDrop;

    /**
     * Drops a database.
     *
     * @param string $Name The name of the database to drop.
     *
     * @return \vDesk\DataProvider\Expression\IDrop The current instance for further chaining.
     */
    public function Database(string $Name): IDrop;

    /**
     * Drops an index.
     *
     * @param string $Name The name of the index to drop.
     *
     * @return \vDesk\DataProvider\Expression\IDrop The current instance for further chaining.
     */
    public function Index(string $Name): IDrop;

    /**
     * Applies an ON statement for dropping indices.
     *
     * @param string $Table The name of the target table to drop the index off.
     *
     * @return \vDesk\DataProvider\Expression\IDrop The current instance for further chaining.
     */
    public function On(string $Table): IDrop;

}