<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL;

use vDesk\DataProvider\IProvider;
use vDesk\DataProvider\IResult;
use vDesk\DataProvider\SQLException;
use vDesk\Data\IManagedModel;
use vDesk\Struct\Type;

/**
 * Abstract data-provider for MySQL and MariaDB databases.
 *
 * @package vDesk\DataProvider\Provider
 * @author  Kerry Holz<DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
final class Provider implements IProvider {

    /**
     * Regular expression to extract the table- and column name of a field descriptor.
     */
    public const Separator = "/^(\w+)\.(\w+)$/";

    /**
     * The format for storing \DateTime values in a MySQL conform format.
     */
    public const Format = "Y-m-d\TH:i:s";

    /**
     * The reserved key words of the Provider\MariaDB.
     */
    public const Reserved = [
        "READ",
        "WRITE",
        "DELETE",
        "UPDATE",
        "ADD",
        "AND",
        "BEFORE",
        "BY",
        "CALL",
        "CASE",
        "CONDITION",
        "DESC",
        "DESCRIBE",
        "FROM",
        "GROUP",
        "IN",
        "INDEX",
        "INSERT",
        "INTERVAL",
        "IS",
        "KEY",
        "LIKE",
        "LIMIT",
        "LONG",
        "MATCH",
        "NOT",
        "OPTION",
        "OR",
        "ORDER",
        "PARTITION",
        "REFERENCES",
        "SELECT",
        "TABLE",
        "TO",
        "WHERE",
        "BINARY"
    ];

    /**
     * The default port MariaDB-servers usually use.
     */
    public const Port = 3306;

    /**
     * The default charset of the connection collation.
     */
    public const Collation = "utf8mb4";

    /**
     * Database null value.
     */
    public const NULL = "NULL";


    /** @var \mysqli|null */
    private ?\mysqli $Provider;

    /**
     * Initializes a new instance of the IProvider class.
     *
     * @param string $Server    The address of the target SQL-server.
     * @param string $User      The name of the user of the target SQL-server.
     * @param string $Password  The password of the user of the target SQL-server.
     * @param int    $Port      The port to use for the connection-socket.
     * @param string $Collation The charset of the collation of the connection.
     */
    public function __construct(string $Server, string $User, string $Password, ?int $Port = self::Port, ?string $Collation = self::Collation) {
        $this->Provider = new \mysqli($Server, $User, $Password, null, $Port ?? self::Port);
        $this->Provider->set_charset($Collation ?? self::Collation);
    }

    /**
     * Retrieves the last auto generated ID of an INSERT-SQL-Statement.
     *
     * @return int The last insert ID.
     */
    public function LastInsertID(): int {
        return (int)$this->Provider->insert_id;
    }
    
    /**
     * Prepares a SQL-statement to execute on a SQL-server.
     *
     * @param string $Statement The SQL-statement to execute.
     * @param bool   $Buffered  Determines whether the result-set will be buffered.
     *
     * @return \vDesk\DataProvider\MySQL\Statement The prepared statement to execute.
     */
    public function Prepare(string $Statement, bool $Buffered = true): Statement {
        return new Statement($this->Provider->prepare($Statement), $Buffered);
    }
    
    /**
     * Executes a SQL-statement on the database-server.
     *
     * @param string $Statement The SQL-statement to execute.
     * @param bool   $Buffered  Determines whether the result-set will be buffered.
     *
     * @return \vDesk\DataProvider\IResult The result-set yielding the values the SQL-server returned from the specified statement.
     */
    public function Execute(string $Statement, bool $Buffered = true): IResult {

        //Flush previous resultsets.
        while($this->Provider->more_results()) {
            $this->Provider->next_result();
        }

        //Execute statement.
        if(!$this->Provider->real_query($Statement)) {
            throw new SQLException($this->Provider->error . $Statement);
        }

        //Check if the result should be buffered
        $Result = $Buffered ? $this->Provider->store_result() : $this->Provider->use_result();

        return $Result instanceof \mysqli_result
            ? new Result($Result, $Buffered)
            : new \vDesk\DataProvider\Result();
    }
    
    /**
     * Executes an stored procedure on the sql-server.
     *
     * @param string $Procedure The name of the procedure to execute.
     * @param array  $Arguments The list of arguments to pass to the procedure.
     *
     * @return \vDesk\DataProvider\MySQL\Result The IResult containing the results of the executed procedure.
     */
    public function Call(string $Procedure, array $Arguments): IResult {
        return $this->Execute("CALL {$Procedure}(" . ((\count($Arguments) > 0) ? "'" . implode("','", $Arguments) . "'" : "") . ");", true);
    }

    /**
     * Escapes special characters in a string for use in an SQL statement, taking into account the current charset of the connection
     *
     * @param string $String The string to escape.
     *
     * @return string The escaped string.
     */
    public function Escape(string $String): string {
        return $this->Provider->real_escape_string($String);
    }

    /**
     * Escapes reserved words in a field according to the current database-specification.
     *
     * @param string $Field The field to escape.
     *
     * @return string The escaped field.
     */
    public function EscapeField(string $Field): string {
        return \in_array(\strtoupper(\trim($Field)), self::Reserved)
            ? "`{$Field}`"
            : $Field;
    }

    /**
     * Sanitizes a value according to the MySQL database-specification.
     *
     * @param mixed|\vDesk\Data\IManagedModel $Value The value to sanitize.
     *
     * @return mixed The sanitized value.
     */
    public function Sanitize($Value) {
        if($Value instanceof IManagedModel) {
            return $this->Sanitize($Value->ID());
        }
        switch(Type::Of($Value)) {
            case Type::String:
                return "'{$this->Escape($Value)}'";
            case Type::Bool:
            case Type::Boolean:
                return (int)$Value;
            case Type::Null:
                return static::NULL;
            case Type::Object:
            case Type::Array:
                return "'" . \json_encode($Value) . "'";
            case \DateTime::class:
                return "'{$Value->format(self::Format)}'";
        }
        return $Value;
    }
    
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
     * @return \vDesk\DataProvider\MySQL\Transaction The transaction to execute.
     */
    public function Transact(string $Statement, bool $Buffered = false, ?string $Name = null, bool $AutoRollback = true, bool $AutoCommit = true): Transaction {
        return new Transaction($this->Provider, $Statement, $Buffered, $Name, $AutoRollback, $AutoCommit);
    }

    /**
     * Escapes reserved words in a field according to the current database-specification.
     *
     * @param string $Field The field to escape.
     *
     * @return string The escaped field.
     */
    public function SanitizeField(string $Field): string {
        $Matches = [];
        return (int)\preg_match(self::Separator, $Field, $Matches) > 0
            ? $this->EscapeField($Matches[1]) . "." . $this->EscapeField($Matches[2])
            : $this->EscapeField($Field);
    }

}