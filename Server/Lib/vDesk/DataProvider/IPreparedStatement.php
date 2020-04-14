<?php
declare(strict_types=1);

namespace vDesk\DataProvider;

/**
 * Interface IPreparedStatement that represents a ...
 *
 * @package vDesk\DataProvider
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
interface IPreparedStatement {

    /**
     * @param string $Types
     * @param mixed  ...$Values
     *
     * @return \vDesk\DataProvider\IPreparedStatement The instance itself for further chaining.
     */
    public function Apply(string $Types, ...$Values): IPreparedStatement;

    /**
     * Executes the SQL-statement of the IPreparedStatement against a database.
     *
     * @return \vDesk\DataProvider\IResult
     */
    public function Execute(): IResult;

}