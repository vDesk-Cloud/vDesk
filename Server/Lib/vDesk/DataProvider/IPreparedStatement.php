<?php
declare(strict_types=1);

namespace vDesk\DataProvider;

/**
 * Interface IPreparedStatement that represents a ...
 *
 * @package vDesk\DataProvider
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
interface IPreparedStatement {
    
    /**
     * Applies a set of values to the IPreparedStatement.
     *
     * @param mixed  ...$Values The values to apply.
     *
     * @return \vDesk\DataProvider\IPreparedStatement The instance itself for further chaining.
     */
    public function Apply(...$Values): IPreparedStatement;
    
    /**
     * Executes the SQL-statement of the IPreparedStatement against a database.
     *
     * @return \vDesk\DataProvider\IResult
     */
    public function Execute(): IResult;
    
}