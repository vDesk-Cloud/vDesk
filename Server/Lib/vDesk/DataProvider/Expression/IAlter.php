<?php
declare(strict_types=1);

namespace vDesk\DataProvider\Expression;

use vDesk\DataProvider\IExpression;

/**
 * Interface that represents a ALTER SQL expression.
 *
 * @package vDesk\DataProvider\Expression
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IAlter extends IExpression {
    
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
     * Renames multiple columns of the table to alter.
     *
     * @param string[] $Columns A key-value separated array of columns and their new names.
     *
     * @return \vDesk\DataProvider\Expression\IAlter The current instance for further chaining.
     */
    public function Rename(array $Columns): IAlter;
    
    /**
     * Modifies multiple columns and indexes of the table to alter.
     *
     * @param array[] $Columns The columns to modify.
     * @param array[] $Indexes The indexes to modify.
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
    
    /**
     * Alters a database.
     *
     * @param string $Name The name of the database to alter.
     *
     * @return \vDesk\DataProvider\Expression\IAlter The current instance for further chaining.
     */
    public function Database(string $Name): IAlter;
    
}