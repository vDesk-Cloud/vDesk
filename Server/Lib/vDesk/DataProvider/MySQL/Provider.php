<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL;

use vDesk\DataProvider\IResult;
use vDesk\DataProvider\SQLException;

/**
 * Abstract data-provider for MySQL and MariaDB databases.
 *
 * @package vDesk\DataProvider\Provider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Provider extends \vDesk\DataProvider\AnsiSQL\Provider {

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
    public const Charset = "utf8mb4";

    /**
     * The underlying mysqli connection instance of the Provider.
     *
     * @var \mysqli|null
     */
    protected ?\mysqli $Provider;

    /**
     * Initializes a new instance of the Provider class.
     *
     * @param string      $Server     Initializes the Provider with the specified address of the target SQL-server.
     * @param string      $User       Initializes the Provider with the specified name of the user of the target SQL-server.
     * @param string      $Password   Initializes the Provider with the specified password of the user of the target SQL-server.
     * @param null|int    $Port       Initializes the Provider with the specified port to use for the connection-socket.
     * @param null|string $Charset    Initializes the Provider with the specified charset of the connection.
     * @param bool        $Persistent Initializes the Provider with the specified flag indicating whether to use a persistent connection.
     */
    public function __construct(string $Server, string $User, string $Password, ?int $Port = self::Port, ?string $Charset = self::Charset, bool $Persistent = true) {
        if($Persistent) {
            $Server = "p:{$Server}";
        }
        $this->Provider = new \mysqli($Server, $User, $Password, null, $Port ?? self::Port);
        $this->Provider->set_charset($Charset ?? self::Charset);
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
     * @throws \vDesk\DataProvider\SQLException
     */
    public function Execute(string $Statement, bool $Buffered = true): IResult {

        //Flush previous resultsets.
        while($this->Provider->more_results()) {
            $this->Provider->next_result();
        }

        //Execute statement.
        if(!$this->Provider->real_query($Statement)) {
            throw new SQLException($this->Provider->error, $this->Provider->errno);
        }

        //Check if the result should be buffered
        $Result = $Buffered ? $this->Provider->store_result() : $this->Provider->use_result();

        return $Result instanceof \mysqli_result
            ? new Result($Result, $Buffered)
            : new \vDesk\DataProvider\Result();
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

}