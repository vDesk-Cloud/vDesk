<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL;

use vDesk\Data\IManagedModel;
use vDesk\DataProvider\IResult;
use vDesk\DataProvider\SQLException;
use vDesk\IO\IOException;
use vDesk\Struct\Type;

/**
 * Abstract data-provider for PostgreSQL databases.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Provider extends \vDesk\DataProvider\AnsiSQL\Provider {

    /**
     * The default port PgSQL-servers usually use.
     */
    public const Port = 5432;

    /**
     * The default charset of the connection collation.
     */
    public const Charset = "UTF8";

    /**
     * The underlying pgsql connection resource.
     *
     * @var false|resource
     */
    protected mixed $Provider;

    /**
     * Initializes a new instance of the Provider class.
     *
     * @param string      $Server     Initializes the Provider with the specified address of the target SQL-server.
     * @param string      $User       Initializes the Provider with the specified name of the user of the target SQL-server.
     * @param string      $Password   Initializes the Provider with the specified password of the user of the target SQL-server.
     * @param null|string $Database   Initializes the Provider with the specified database of the target SQL-server.
     * @param null|int    $Port       Initializes the Provider with the specified port to use for the connection-socket.
     * @param null|string $Charset    Initializes the Provider with the specified charset of the connection.
     * @param bool        $Persistent Initializes the Provider with the specified flag indicating whether to use a persistent connection.
     *
     * @throws \vDesk\IO\IOException Thrown if the connection couldn't be established.
     */
    public function __construct(
        string  $Server,
        string  $User,
        string  $Password,
        ?string $Database = null,
        ?int    $Port = self::Port,
        ?string $Charset = self::Charset,
        bool    $Persistent = true
    ) {
        $Credentials    = "host={$Server} port={$Port} user={$User} password={$Password}" . ($Database !== null ? " dbname={$Database}" : "");
        $this->Provider = $Persistent ? \pg_pconnect($Credentials) : \pg_connect($Credentials);
        if($this->Provider === false) {
            throw new IOException("Couldn't establish connection to server: " . \pg_last_error());
        }
        \pg_set_client_encoding($this->Provider, $Charset ?? static::Charset);
    }

    /**
     * Retrieves the last auto generated ID of an INSERT-SQL-Statement.
     *
     * @return int The last insert ID.
     */
    public function LastInsertID(): int {
        return (int)$this->Execute("SELECT LASTVAL()")->ToValue();
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
     * @param bool   $Buffered  Flag indicating whether to buffer the result set. This parameter is being ignored due to PgSQL doesn't support streaming of result sets.
     *
     * @return \vDesk\DataProvider\IResult The result-set yielding the values the SQL-server returned from the specified statement.
     *
     * @throws \vDesk\DataProvider\SQLException Thrown if the execution of the statement failed.
     */
    public function Execute(string $Statement, bool $Buffered = true): IResult {
        $Result = \pg_query($this->Provider, $Statement);
        if(!$Result) {
            throw new SQLException(\pg_last_error($this->Provider));
        }
        if(\pg_num_rows($Result) > 0) {
            return new Result($Result);
        }
        return new \vDesk\DataProvider\Result();
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
        if($Field === "*"){
            return $Field;
        }
        return \pg_escape_identifier($this->Provider, $Field);
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

    /**
     * @inheritDoc
     */
    public function Close(): void {
        \pg_close($this->Provider);
    }

}