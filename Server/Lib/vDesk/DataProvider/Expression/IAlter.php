<?php
declare(strict_types=1);

namespace vDesk\DataProvider\Expression;

use vDesk\DataProvider\IExpression;

/**
 * Interface for abstract SQL "ALTER" Expressions.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IAlter extends IExpression {

    /**
     * Alters a database.
     *
     * @param string $Name The  $Name of the database to alter.
     *
     * @return \vDesk\DataProvider\Expression\IAlter The current instance for further chaining.
     */
    public function Database(string $Name): IAlter;

    /**
     * Alters a schema.
     *
     * @param string $Name The  name of the schema to alter.
     *
     * @return \vDesk\DataProvider\Expression\IAlter The current instance for further chaining.
     */
    public function Schema(string $Name): IAlter;

    /**
     * Alters a table.
     *
     * @param string $Name The name of the table to alter.
     *
     * @return \vDesk\DataProvider\Expression\IAlter The current instance for further chaining.
     */
    public function Table(string $Name): IAlter;

    /**
     * Applies multiple "ADD COLUMN/INDEX" statements to the Expression.
     *
     * @param array $Columns The columns to add.
     * @param array $Indexes The indexes to add.
     *
     * @return \vDesk\DataProvider\Expression\IAlter The current instance for further chaining.
     */
    public function Add(array $Columns, array $Indexes = []): IAlter;

    /**
     * Applies a "RENAME" statement to the Expression.
     *
     * @param string $Name The new name of the entity to rename.
     *
     * @return \vDesk\DataProvider\Expression\IAlter The current instance for further chaining.
     */
    public function Rename(string $Name): IAlter;

    /**
     * Applies multiple "MODIFY COLUMN" and "RENAME COLUMN/INDEX" statements to the Expression.
     * Implementations should treat string values in the $Columns parameter as renaming attempts.
     *
     * @param array $Columns The columns to modify or rename.
     * @param array $Indexes A key-value separated array of indexes to rename.
     *
     * @return \vDesk\DataProvider\Expression\IAlter The current instance for further chaining.
     */
    public function Modify(array $Columns, array $Indexes = []): IAlter;

    /**
     * Drops multiple columns and indexes of the table to alter.
     *
     * @param string[] $Columns The columns to drop.
     * @param string[] $Indexes The indexes to drop.
     *
     * @return \vDesk\DataProvider\Expression\IAlter The current instance for further chaining.
     */
    public function Drop(array $Columns, array $Indexes = []): IAlter;

}