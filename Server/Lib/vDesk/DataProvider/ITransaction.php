<?php
declare(strict_types=1);

namespace vDesk\DataProvider;

/**
 * Interface ITransaction that represents a ...
 *
 * @package vDesk\DataProvider
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
interface ITransaction {
    
    /**
     * Executes a further SQL-statement on a SQL-server.
     *
     * @param string      $Statement The SQL-statement to execute.
     * @param null|string $Name      The name of the SQL-statement.
     *
     * @return \vDesk\DataProvider\ITransaction The current instance for further chaining.
     */
    public function Transact(string $Statement, ?string $Name = null): ITransaction;
    
    /**
     * Executes the SQL-statement of the ITransaction on a SQL-server.
     *
     * @param string $Statement The statement to explicitly execute; if omitted, the first specified statement should be executed in a
     *                          FIFO-order.
     *
     * @return \vDesk\DataProvider\IResult The result-set yielding the values the SQL-server returned from the specified statement.
     */
    public function Execute(string $Statement): IResult;
    
    /**
     * Executes the SQL-statement of the ITransaction on a SQL-server.
     *
     * @param string[] $Statements The statements to explicitly execute; if omitted, all statements will be executed.
     *
     * @return iterable The result-sets yielding the values the SQL-server returned from the specified statements.
     */
    public function ExecuteAll(string ...$Statements): iterable;
    
    /**
     * Commits the changes done by the statements of the ITransaction.
     *
     * @param string[] $Statements The statements to explicitly commit; if omitted, all statements will be committed.
     *
     * @return bool True if every specified statement has been successfully committed; otherwise, false.
     */
    public function Commit(string ...$Statements): bool;
    
    /**
     * Reverts any uncommitted changes.
     *
     * @param string[] $Statements The statements to explicitly revert; if omitted, all statements will be reverted.
     *
     * @return bool True if every specified statement has been successfully reverted; otherwise, false.
     */
    public function Rollback(string ...$Statements): bool;
    
    /**
     * Sets a callback-function to execute if an error has occurred or if the transaction wasn't successful.
     * The scope of the callback-closure will be bound to the current instance.
     *
     * @param callable $Callback The callback to execute.
     *
     * @return \vDesk\DataProvider\ITransaction  The current instance for further chaining.
     */
    public function Error(callable $Callback): ITransaction;
    
    /**
     * Sets a callback-function to execute the transaction was successful.
     * The scope of the callback-closure will be bound to the current instance.
     *
     * @param callable $Callback The callback to execute.
     *
     * @return \vDesk\DataProvider\ITransaction  The current instance for further chaining.
     */
    public function Success(callable $Callback): ITransaction;
    
}