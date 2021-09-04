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
     * Alters(renames) a database.
     *
     * @param string $Old The old name of the database to alter.
     * @param string $New The new name of the database to alter.
     *
     * @return \vDesk\DataProvider\Expression\IAlter The current instance for further chaining.
     */
    public function Database(string $Old, string $New): IAlter;

    /**
     * Alters(renames) a schema.
     *
     * @param string $Old The old name of the schema to alter.
     * @param string $New The new name of the schema to alter.
     *
     * @return \vDesk\DataProvider\Expression\IAlter The current instance for further chaining.
     */
    public function Schema(string $Old, string $New): IAlter;

    /**
     * Alters a table.
     *
     * @param string $Name The name of the table to alter.
     *
     * @return \vDesk\DataProvider\Expression\IAlter The current instance for further chaining.
     */
    public function Table(string $Name): IAlter;

    /**
     * Adds multiple columns and indexes to the table to alter.
     *
     * @param array[] $Columns The columns to add.
     * @param array[] $Indexes The indexes to add.
     *
     * @return \vDesk\DataProvider\Expression\IAlter The current instance for further chaining.
     */
    public function Add(array $Columns, array $Indexes = []): IAlter;

    /**
     * Renames multiple columns and indexes of the table to alter.
     *
     * @param string[] $Columns A key-value separated array of columns and their new names.
     *
     * @return \vDesk\DataProvider\Expression\IAlter The current instance for further chaining.
     */
    public function Rename(array $Columns, array $Indexes = []): IAlter;

    /**
     * Alters multiple columns of the table to alter.
     *
     * @param array[] $Columns The columns to modify.
     *
     * @return \vDesk\DataProvider\Expression\IAlter The current instance for further chaining.
     */
    public function Modify(array $Columns): IAlter;

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