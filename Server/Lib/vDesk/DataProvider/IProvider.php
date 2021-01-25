<?php
declare(strict_types=1);

namespace vDesk\DataProvider;

/**
 * Interface for vendor specific SQL data-providers.
 * Provides functionality for executing queries and procedures on a database.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IProvider {
    
    /**
     * Initializes a new instance of the IProvider class.
     *
     * @param string      $Server    The address of the target SQL-server.
     * @param string      $User      The name of the user of the target SQL-server.
     * @param string      $Password  The password of the user of the target SQL-server.
     * @param null|int    $Port      The port to use for the connection-socket.
     * @param null|string $Collation The charset of the collation of the connection.
     */
    public function __construct(string $Server, string $User, string $Password, ?int $Port, ?string $Collation);
    
    /**
     * Retrieves the last auto generated ID of an INSERT-SQL-Statement.
     *
     * @return int The last insert ID.
     */
    public function LastInsertID(): int;
    
    /**
     * Executes a SQL-query on a SQL-server.
     *
     * @param string $Statement The SQL-statement to execute.
     * @param bool   $Buffered  Determines whether the result-set will be buffered.
     *
     * @return \vDesk\DataProvider\IResult The result-set yielding the values the SQL-server returned from the specified statement.
     */
    public function Execute(string $Statement, bool $Buffered = true): IResult;
    
    /**
     * Executes an stored procedure on a SQL-server.
     *
     * @param string $Procedure The name of the procedure to execute.
     * @param array  $Arguments The list of arguments to pass to the procedure.
     *
     * @return \vDesk\DataProvider\IResult The IResult containing the results of the executed procedure.
     */
    public function Call(string $Procedure, array $Arguments): IResult;
    
    /**
     * Escapes special characters in a string according to the current database-specification.
     *
     * @param string $String The string to escape.
     *
     * @return string The escaped string.
     */
    public function Escape(string $String): string;
    
    /**
     * Escapes a field if the field contains a reserved word according to the current database-specification.
     *
     * @param string $Field The field to escape.
     *
     * @return string The escaped field.
     */
    public function EscapeField(string $Field): string;
    
    /**
     * Sanitizes a value according to the current database-specification.
     *
     * @param mixed $Value The value to sanitize.
     *
     * @return mixed The sanitized value.
     */
    public function Sanitize(mixed $Value): mixed;
    
    /**
     * Escapes reserved words in a pair of table and field, separated by the  table.column separator of the current configured DataProvider or a single field
     * according to the current database-specification.
     *
     * @param string $Field The pair of table and field to sanitize.
     *
     * @return string The sanitized pair of table and field.
     */
    public function SanitizeField(string $Field): string;
    
    /**
     * Prepares a SQL-statement to execute on a SQL-server.
     *
     * @param string $Statement The SQL-statement to execute.
     * @param bool   $Buffered  Determines whether the result-set will be buffered.
     *
     * @return \vDesk\DataProvider\IPreparedStatement The prepared statement to execute.
     */
    public function Prepare(string $Statement, bool $Buffered = true): IPreparedStatement;
    
    /**
     * Begins a SQL-transaction to execute on a SQL-server.
     *
     * @param string      $Statement    The SQL-transaction-statement to execute.
     * @param bool        $Buffered     Determines whether the result-set will be buffered.
     * @param null|string $Name         The name of the SQL-statement.
     * @param bool        $AutoRollback Determines whether the changes of the transaction will be rolled back automatically if an error has
     *                                  occurred or the transaction was unsuccessful.
     * @param bool        $AutoCommit   Determines whether the changes of the transaction will be committed automatically if the
     *                                  transaction was successful.
     *
     * @return \vDesk\DataProvider\ITransaction The transaction to execute.
     */
    public function Transact(string $Statement, bool $Buffered = false, ?string $Name = null, bool $AutoRollback = true, bool $AutoCommit = true): ITransaction;
    
}