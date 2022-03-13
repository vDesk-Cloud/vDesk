<?php
declare(strict_types=1);

namespace vDesk\DataProvider\Expression;

use vDesk\DataProvider\IExpression;

/**
 * Interface for abstract SQL "CREATE" Expressions.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface ICreate extends IExpression {

    /**
     * Creates a database.
     *
     * @param string $Name The name of the database to create.
     *
     * @return \vDesk\DataProvider\Expression\ICreate The current instance for further chaining.
     */
    public function Database(string $Name): ICreate;

    /**
     * Creates a schema.
     *
     * @param string $Name The name of the schema to create.
     *
     * @return \vDesk\DataProvider\Expression\ICreate The current instance for further chaining.
     */
    public function Schema(string $Name): ICreate;

    /**
     * Creates a table.
     *
     * @param string $Name The name of the table to create.
     *
     * @return \vDesk\DataProvider\Expression\ICreate The current instance for further chaining.
     */
    public function Table(string $Name): ICreate;

    /**
     * Creates an index.
     *
     * @param string $Name   The name of the index to create.
     * @param bool   $Unique Flag indicating whether to create an unique index.
     *
     *
     * @return \vDesk\DataProvider\Expression\ICreate The current instance for further chaining.
     */
    public function Index(string $Name, bool $Unique): ICreate;

    /**
     * Applies an ON statement for creating indices.
     *
     * @param string $Table  The name of the target table to create an index on.
     * @param array  $Fields $Fields The fields of the index.
     *
     * @return \vDesk\DataProvider\Expression\ICreate The current instance for further chaining.
     */
    public function On(string $Table, array $Fields): ICreate;

}