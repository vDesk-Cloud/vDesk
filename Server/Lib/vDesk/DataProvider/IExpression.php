<?php
declare(strict_types=1);

namespace vDesk\DataProvider;

/**
 * Interface that represents a SQL-Expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
interface IExpression {

    /**
     * Executes the expression on a SQL-Server.
     *
     * @param bool $Buffered Flag indicating whether the result-set will be buffered.
     *
     * @return \vDesk\DataProvider\IResult The result-set yielding the values the SQL-Server returned by executing the IExpression.
     */
    public function Execute(bool $Buffered = true): IResult;

    /**
     * Retrieves a string representation of the IExpression.
     *
     * @return string The string representation of the IExpression.
     */
    public function __toString();

    /**
     * Executes the expression on a SQL-Server.
     *
     * @return \vDesk\DataProvider\IResult The result-set yielding the values the SQL-Server returned by executing the IExpression.
     */
    public function __invoke();

}