<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL;

use vDesk\DataProvider\IResult;
use vDesk\IO\IOException;

/**
 * Abstract data-provider for MsSQL databases.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Provider extends \vDesk\DataProvider\AnsiSQL\Provider {

    public const Reserved = ["USERS", "PUBLIC", "FILE"] + parent::Reserved;

    /**
     * The default port MsSQL-servers usually use.
     */
    public const Port = 1433;

    /**
     * The default charset of the connection collation.
     */
    public const Charset = "UTF-8";

    /**
     * The underlying pgsql connection resource.
     *
     * @var false|resource
     */
    protected mixed $Provider;

    /**
     * Enumeration of characters to escape within SQL statements.
     */
    public const Escape = ["'"];

    /**
     * Enumeration of escaped control characters.
     */
    public const Escaped = ["''"];

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
        ?string $Database = "master",
        ?int    $Port = self::Port,
        ?string $Charset = self::Charset,
        bool    $Persistent = false
    ) {
        $this->Provider = \sqlsrv_connect(
            "tcp:{$Server}, " . ($Port ?? self::Port),
            [
                "ConnectionPooling"    => $Persistent,
                "UID"                  => $User,
                "PWD"                  => $Password,
                "Database"             => $Database ?? "master",
                "CharacterSet"         => $Charset ?? self::Charset,
                "ReturnDatesAsStrings" => true
            ]
        );
        if($this->Provider === false) {
            throw new IOException("Couldn't establish connection to server: " . \json_encode(\sqlsrv_errors()));
        }
    }

    /**
     * Retrieves the last auto generated ID of an INSERT-SQL-Statement.
     *
     * @return int The last insert ID.
     */
    public function LastInsertID(): int {
        return (int)$this->Execute("SELECT @@IDENTITY", false)->ToValue();
    }

    /**
     * Prepares a SQL-statement to execute on a SQL-server.
     *
     * @param string $Statement The SQL-statement to execute.
     * @param string $Name      The optional name of the statement.
     *
     * @return \vDesk\DataProvider\MsSQL\Statement The prepared statement to execute.
     */
    public function Prepare(string $Statement, string $Name = ""): Statement {
        return new Statement($this->Provider, $Statement, $Name);
    }

    /**
     * Executes a SQL-statement on the database-server.
     *
     * @param string $Statement The SQL-statement to execute.
     * @param bool   $Buffered  Flag indicating whether to buffer the result set.
     *
     * @return \vDesk\DataProvider\IResult The result-set yielding the values the SQL-server returned from the specified statement.
     */
    public function Execute(string $Statement, bool $Buffered = true): IResult {
        $Result = \sqlsrv_query($this->Provider, $Statement, [], ["Scrollable" => $Buffered ? \SQLSRV_CURSOR_CLIENT_BUFFERED : \SQLSRV_CURSOR_STATIC]);
        return $Result ? new Result($Result, $Buffered) : new \vDesk\DataProvider\Result();
    }

    /**
     * @inheritDoc
     */
    public function Call(string $Procedure, array $Arguments): IResult {
        return $this->Execute("EXECUTE {$this->Escape($Procedure)} " . \implode(", ", \array_map(fn($Argument) => $this->Sanitize($Argument), $Arguments)));
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
            ? "[{$Field}]"
            : $Field;
    }

    /**
     * Begins a SQL-transaction to execute on a SQL-server.
     *
     * @param string      $Statement    The SQL-transaction-statement to execute.
     * @param bool        $Buffered     Determines whether the result-set will be buffered.
     * @param null|string $Name         The name of the SQL-statement.
     * @param bool        $AutoRollback Determines whether the changes of the transaction will be rolled back automatically if an error has occurred or the transaction was unsuccessful.
     * @param bool        $AutoCommit   Determines whether the changes of the transaction will be committed automatically if the transaction was successful.
     *
     * @return \vDesk\DataProvider\MsSQL\Transaction The transaction to execute.
     */
    public function Transact(string $Statement, bool $Buffered = false, ?string $Name = null, bool $AutoRollback = true, bool $AutoCommit = true): Transaction {
        return new Transaction($this->Provider, $Statement, $Buffered, $Name, $AutoRollback, $AutoCommit);
    }

    /**
     * @inheritDoc
     */
    public function Close(): void {
        \sqlsrv_close($this->Provider);
    }

}