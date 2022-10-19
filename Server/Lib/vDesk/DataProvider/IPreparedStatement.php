<?php
declare(strict_types=1);

namespace vDesk\DataProvider;

/**
 * Interface for prepared statements.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IPreparedStatement {
    
    /**
     * Applies a set of values to the IPreparedStatement.
     *
     * @param mixed  ...$Values The values to apply.
     *
     * @return \vDesk\DataProvider\IPreparedStatement The instance itself for further chaining.
     */
    public function Apply(mixed ...$Values): IPreparedStatement;
    
    /**
     * Executes the SQL-statement of the IPreparedStatement against a database.
     *
     * @return \vDesk\DataProvider\IResult
     */
    public function Execute(): IResult;
    
}