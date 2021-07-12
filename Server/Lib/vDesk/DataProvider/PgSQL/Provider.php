<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL;

use vDesk\DataProvider\IResult;
use vDesk\IO\IOException;

/**
 * Abstract data-provider for PostgreSQL databases.
 *
 * @package vDesk\DataProvider\PgSQL
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Provider extends \vDesk\DataProvider\AnsiSQL\Provider {

    /**
     * The default port PgSQL-servers usually use.
     */
    public const Port = 3306;

    /**
     * The default charset of the connection collation.
     */
    public const Charset = "UTF8";

    /**
     * The underlying pgsql connection resource.
     *
     * @var false|resource
     */
    protected $Provider;

    /**
     * Initializes a new instance of the Provider class.
     *
     * @param string      $Server     Initializes the Provider with the specified address of the target SQL-server.
     * @param string      $User       Initializes the Provider with the specified name of the user of the target SQL-server.
     * @param string      $Password   Initializes the Provider with the specified password of the user of the target SQL-server.
     * @param null|int    $Port       Initializes the Provider with the specified port to use for the connection-socket.
     * @param null|string $Charset    Initializes the Provider with the specified charset of the connection.
     * @param bool        $Persistent Initializes the Provider with the specified flag indicating whether to use a persistent connection.
     *
     * @throws \vDesk\IO\IOException Thrown if the connection couldn't be established.
     */
    public function __construct(string $Server, string $User, string $Password, ?int $Port = self::Port, ?string $Charset = self::Charset, bool $Persistent = true) {
        $this->Provider = $Persistent
            ? \pg_pconnect("host=${$Server} port=${$Port} user=${$User} password=${$Password}")
            : \pg_connect("host=${$Server} port=${$Port} user=${$User} password=${$Password}");
        if($this->Provider === false) {
            throw new IOException("Couldn't connect to server.");
        }
    }

    /**
     * Retrieves the last auto generated ID of an INSERT-SQL-Statement.
     *
     * @return int The last insert ID.
     */
    public function LastInsertID(): int {
        return (int)\pg_last_oid($this->Provider);
    }

    /**
     * Prepares a SQL-statement to execute on a SQL-server.
     *
     * @param string $Statement The SQL-statement to execute.
     * @param string $Name      The optional name of the statement.
     *
     * @return \vDesk\DataProvider\PgSQL\Statement The prepared statement to execute.
     */
    public function Prepare(string $Statement, string $Name = ""): Statement {
        return new Statement($this->Provider, $Statement, $Name);
    }

    /**
     * Executes a SQL-statement on the database-server.
     *
     * @param string $Statement The SQL-statement to execute.
     *
     * @return \vDesk\DataProvider\IResult The result-set yielding the values the SQL-server returned from the specified statement.
     */
    public function Execute(string $Statement): IResult {
        $Result = \pg_query($this->Provider, $Statement);
        return $Result ? new Result($Result) : new \vDesk\DataProvider\Result();
    }

    /**
     * Escapes special characters in a string for use in an SQL statement, taking into account the current charset of the connection
     *
     * @param string $String The string to escape.
     *
     * @return string The escaped string.
     */
    public function Escape(string $String): string {
        return \pg_escape_string($this->Provider, $String);
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
            ? \pg_escape_identifier($this->Provider, $Field)
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
     * @return \vDesk\DataProvider\PgSQL\Transaction The transaction to execute.
     */
    public function Transact(string $Statement, bool $Buffered = false, ?string $Name = null, bool $AutoRollback = true, bool $AutoCommit = true): Transaction {
        return new Transaction($this->Provider, $Statement, $Buffered, $Name, $AutoRollback, $AutoCommit);
    }

}