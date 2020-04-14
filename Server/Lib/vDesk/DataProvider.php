<?php
declare(strict_types=1);

namespace vDesk;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Expression\Functions;
use vDesk\DataProvider\IPreparedStatement;
use vDesk\DataProvider\IProvider;
use vDesk\DataProvider\IResult;
use vDesk\DataProvider\ITransaction;
use vDesk\Struct\StaticSingleton;

/**
 * Provides abstract database access.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
final class DataProvider extends StaticSingleton {

    /**
     * Database null value.
     */
    public static $NULL = null;

    /**
     * The IProvider of the DataProvider
     *
     * @var  null|\vDesk\DataProvider\IProvider
     */
    public static ?IProvider $Provider;

    /**
     * The Expressions of the DataProvider.
     *
     * @var \vDesk\DataProvider\Expression|null
     */
    public static ?Expression $Expression;

    /**
     * The Functions of the DataProvider
     *
     * @var \vDesk\DataProvider\Expression\Functions|null
     */
    public static ?Expression\Functions $Functions;

    /**
     * Retrieves the last auto generated ID of an INSERT-SQL-Statement.
     *
     * @return int The last insert ID.
     */
    public static function LastInsertID(): int {
        return self::$Provider->LastInsertID();
    }

    /**
     * Executes a SQL-Statement on the database-server.
     *
     * @param string $Statement The SQL-Statement to execute.
     * @param bool   $Buffered  Flag indicating whether the result-set will be buffered.
     *
     * @return IResult The result-set yielding the values the SQL-server returned from the specified statement.
     */
    public static function Execute(string $Statement, $Buffered = true): IResult {
        return self::$Provider->Execute($Statement, $Buffered);
    }

    /**
     * Calls a Stored-Procedure on the database-server.
     *
     * @param string $Procedure      The Stored-Procedure to call.
     * @param mixed  $Parameters,... A list of arguments to pass to the procedure.
     *
     * @return IResult The result-set.
     */
    public static function Call(string $Procedure, ...$Parameters): IResult {
        return self::$Provider->Call($Procedure, $Parameters);
    }

    /**
     * Escapes special characters in a string according to the current database-specification.
     *
     * @param string $String The string to escape.
     *
     * @return string The escaped string.
     */
    public static function Escape(string $String): string {
        return self::$Provider->Escape($String);
    }

    /**
     * Escapes special characters or reserved words in a field according to the current database-specification.
     *
     * @param string $Field The field to escape.
     *
     * @return string The escaped field.
     */
    public static function EscapeField(string $Field): string {
        return self::$Provider->EscapeField($Field);
    }

    /**
     * Sanitizes a value according to the current database-specification.
     *
     * @param mixed $Value The value to sanitize.
     *
     * @return mixed The sanitized value.
     */
    public static function Sanitize($Value) {
        return self::$Provider->Sanitize($Value);
    }

    /**
     * Sanitizes special characters or reserved words in a field according to the current database-specification.
     *
     * @param mixed $Field The field to sanitize.
     *
     * @return mixed The sanitized field.
     */
    public static function SanitizeField($Field) {
        return self::$Provider->SanitizeField($Field);
    }

    /**
     * Prepares a SQL-statement to execute on a SQL-server.
     *
     * @param string $Statement The SQL-statement to execute.
     * @param bool   $Buffered  Determines whether the result-set will be buffered.
     *
     * @return \vDesk\DataProvider\IPreparedStatement The prepared statement to execute.
     */
    public static function Prepare(string $Statement, $Buffered = true): IPreparedStatement {
        return self::$Provider->Prepare($Statement, $Buffered);
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
     * @return \vDesk\DataProvider\ITransaction The transaction to execute.
     */
    public static function Transact(string $Statement, bool $Buffered = false, ?string $Name = null, bool $AutoRollback = false, bool $AutoCommit = false): ITransaction {
        return self::$Provider->Transact($Statement, $Buffered, $Name, $AutoRollback, $AutoCommit);
    }

    /**
     * Initializes a new instance of the DataProvider class.
     *
     * @param string      $Provider Initializes the DataProvider with the specified data provider.
     * @param string      $Server   Initializes the DataProvider with the specified server address.
     * @param int|null    $Port     Initializes the DataProvider with the specified port.
     * @param string      $User     Initializes the DataProvider with the specified database user.
     * @param string      $Password Initializes the DataProvider with the specified password of the database user.
     * @param string|null $Charset  Initializes the DataProvider with the specified charset.
     */
    protected static function _construct(
        string $Provider = "",
        string $Server = "localhost",
        int $Port = null,
        string $User = "",
        string $Password = "",
        string $Charset = null
    ) {
        $Class          = "vDesk\\DataProvider\\{$Provider}\\Provider";
        self::$Provider = new $Class(
            $Server,
            $User,
            $Password,
            $Port,
            $Charset
        );

        //Populate database specific NULL value.
        self::$NULL = self::$Provider::NULL;

        //Initialize Expressions.
        self::$Expression = new Expression($Provider);

        //Initialize Functions.
        self::$Functions = new Functions($Provider);
    }

}