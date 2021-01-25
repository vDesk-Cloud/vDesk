<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL;

use vDesk\DataProvider\IResult;
use vDesk\DataProvider\ITransaction;
use vDesk\DataProvider;

/**
 * Class MariaDB represents a SQL-transaction usually created by invoking @see \vDesk\Connection\DataProvider::Transact().
 *
 * @package vDesk\DataProvider\Transaction
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
final class Transaction implements ITransaction {
    
    /**
     * THe provider-instance of the Transaction\MariaDB.
     *
     * @var \mysqli
     */
    private \mysqli $Provider;
    
    /**
     * The statements of the Transaction\MariaDB.
     *
     * @var array[]
     */
    private array $Statements = [];
    
    /**
     * Flag indicating whether the resultsets of the MariaDB\Transaction will be buffered.
     *
     * @var bool
     */
    private bool $Bufferd;
    
    /**
     * Flag indicating whether the Transaction\MariaDB has been executed.
     *
     * @var bool
     */
    private bool $Executed = false;
    
    /**
     * Flag indicating whether failed transactions will be automatically rolled back.
     *
     * @var bool
     */
    private bool $AutoRollback;
    
    /**
     * Flag indicating whether successful transactions will be automatically committed.
     *
     * @var bool
     */
    private bool $AutoCommit;
    
    /**
     * The callback-function to execute if a transaction has failed.
     *
     * @var null|callable
     */
    private $OnError;
    
    /**
     * The callback-function to execute if a transaction has succeeded.
     *
     * @var null|callable
     */
    private $OnSuccess;
    
    /**
     * Initializes a new instance of the Transaction\MariaDB class.
     *
     * @param \mysqli     $Provider  Initializes the Transaction\MariaDB with the specified reference of
     *                               the Provider to use.
     * @param string      $Statement The SQL-statement to execute.
     * @param bool        $Buffered  Determines whether the result-set will be buffered.
     * @param null|string $Name      The name of the SQL-statement.
     * @param bool        $AutoRollback
     * @param bool        $AutoCommit
     */
    public function __construct(
        \mysqli $Provider,
        string $Statement,
        bool $Buffered = true,
        ?string $Name = null,
        bool $AutoRollback = false,
        bool $AutoCommit = false
    ) {
        $this->Provider     = $Provider;
        $this->Bufferd      = $Buffered;
        $this->AutoRollback = $AutoRollback;
        $this->AutoCommit   = $AutoCommit;
        $this->Transact($Statement, $Name);
        $this->Provider->autocommit(false);
    }
    
    /**
     * Determines whether a specified SQL-statement manipulates data.
     *
     * @param string $Statement The statement to check.
     *
     * @return bool True if the specified SQL-statement doesn't manipulate data; otherwise, false.
     */
    private static function IsReadOnly(string &$Statement): bool {
        return (bool)strpos($Statement, "SELECT") || (bool)strpos($Statement, "UPDATE");
    }
    
    /**
     * Executes a further SQL-statement on a SQL-server.
     *
     * @param string      $Statement The SQL-statement to execute.
     * @param null|string $Name      The name of the SQL-statement.
     *
     * @return \vDesk\DataProvider\MySQL\Transaction The current instance for further chaining.
     */
    public function Transact(string $Statement, ?string $Name = null): self {
        if(!isset($this->Statements[$Name])) {
            $Name                    = $Name ?? "stmt" . \count($this->Statements);
            $this->Statements[$Name] = [
                "Flags"     => self::IsReadOnly($Statement)
                    ? \MYSQLI_TRANS_START_READ_ONLY
                    : \MYSQLI_TRANS_START_READ_WRITE,
                "Statement" => $Statement
            ];
            $this->Provider->begin_transaction($this->Statements[$Name]["Flags"], $Name);
        }
        return $this;
    }
    
    /**
     * Executes the specified statement.
     *
     * @param string $Statement The statement to explicitly execute; if omitted, the first specified statement will be executed.
     *
     * @return \vDesk\DataProvider\IResult The result-set yielding any values retrieved from the previous executed statement.
     */
    public function Execute(string $Statement): IResult {
        
        if(isset($this->Statements[$Statement])) {
        
        } else if(\count($this->Statements) > 0) {
            if(!($Result = DataProvider::Execute(\current($this->Statements)["Statement"], $this->Bufferd))->Successful()) {
                // Call 'OnError'-listener.
                if(\is_callable($this->OnError)) {
                    ($this->OnError)(\key($this->Statements));
                }
                if($this->AutoRollback) {
                    $this->Rollback();
                }
            } else {
                
                // Call 'OnSuccess'-listener.
                if(\is_callable($this->OnSuccess)) {
                    ($this->OnSuccess)(\key($this->Statements));
                }
                if($this->AutoCommit) {
                    $this->Commit();
                }
                
                // Remove the statement if it has been successfully executed.
                \array_shift($this->Statements);
            }
            return $Result;
        }
        return new DataProvider\Result();
    }
    
    /**
     * Executes the SQL-statement of the ITransaction on a SQL-server.
     *
     * @param string ...$Statements
     *
     * @return iterable The result-sets yielding the values the SQL-server returned from the specified statements.
     */
    public function ExecuteAll(string ...$Statements): iterable {
        
        $amStatements = [];
        
        // Check if statements have been specified.
        if(\count($Statements) > 0) {
            foreach($Statements as $sName) {
                if(isset($this->Statements[$sName])) {
                    $amStatements[] = $this->Statements[$sName];
                }
            }
        } else {
            $amStatements = $this->Statements;
        }
        
        // Execute statements.
        foreach($amStatements as $sName => $amStatement) {
            var_dump($amStatement["Statement"]);
            if(!($oResult = DataProvider::Execute($amStatement["Statement"], $this->Bufferd))->Successful()) {
                // Call 'OnError'-listener.
                if(\is_callable($this->OnError)) {
                    ($this->OnError)($sName);
                    var_dump($oResult);
                }
                if($this->AutoRollback) {
                    $this->Rollback($sName);
                }
            } else {
                
                // Call 'OnSuccess'-listener.
                if(\is_callable($this->OnSuccess)) {
                    ($this->OnSuccess)($sName);
                    var_dump($this->Provider->error);
                }
                if($this->AutoCommit) {
                    $this->Commit($sName);
                }
                // Remove the statement if it has been successfully executed.
                unset($this->Statements[$sName]);
            }
            yield $oResult;
        }
        
    }
    
    /**
     * Reverts any uncommitted changes.
     *
     * @param string ...$Statements
     *
     * @return bool True if every specified statement has been successfully reverted; otherwise, false.
     */
    public function Rollback(string ...$Statements): bool {
        
        $bSuccess = false;
        
        if(\count($Statements) > 0) {
            // Rollback specified statements.
            foreach($Statements as $sName) {
                if(isset($this->Statements[$sName])) {
                    $bSuccess = $this->Provider->rollback($this->Statements[$sName]["Flags"], $sName);
                }
            }
        } else {
            // Rollback all statements if none have been specified.
            foreach($this->Statements as $sName => $amData) {
                $bSuccess = $this->Provider->rollback($amData["Flags"], $sName);
            }
        }
        
        return $bSuccess;
    }
    
    /**
     * Commits the changes done by the statements of the Transaction\MariaDB.
     *
     * @param string ...$Statements
     *
     * @return bool True if every specified statement has been successfully committed; otherwise, false.
     */
    public function Commit(string ...$Statements): bool {
        
        $bSuccess = true;
        
        if(\count($Statements) > 0) {
            // Commit specified statements.
            foreach($Statements as $sName) {
                if(isset($this->Statements[$sName])) {
                    $bSuccess = $this->Provider->commit($this->Statements[$sName]["Flags"], $sName);
                }
            }
        } else {
            // Commit all statements if none have been specified.
            foreach($this->Statements as $sName => $amData) {
                $bSuccess = $this->Provider->commit($amData["Flags"], $sName);
            }
        }
        
        return $bSuccess;
    }
    
    /**
     * Sets a callback-function to execute if an error has occurred or if the transaction wasn't successful.
     * The scope of the callback-closure will be bound to the current instance.
     *
     * @param callable $Callback The callback to execute.
     *
     * @return \vDesk\DataProvider\MySQL\Transaction The current instance for further chaining.
     */
    public function Error(callable $Callback): self {
        $this->OnError = \Closure::bind($Callback, $this);
        return $this;
    }
    
    /**
     * Sets a callback-function to execute if the transaction was successful.
     * The scope of the callback-closure will be bound to the current instance.
     *
     * @param callable $Callback The callback to execute.
     *
     * @return \vDesk\DataProvider\MySQL\Transaction The current instance for further chaining.
     */
    public function Success(callable $Callback): self {
        $this->OnSuccess = \Closure::bind($Callback, $this);
        return $this;
    }
    
    /**
     * Executes a further SQL-statement on a SQL-server.
     *
     * @param string      $Statement The SQL-statement to execute.
     * @param null|string $Name      The name of the SQL-statement.
     *
     * @return \vDesk\DataProvider\MySQL\Transaction The current instance for further chaining.
     */
    public function __invoke(string $Statement, ?string $Name = null): self {
        return $this->Transact($Statement, $Name);
    }
    
    /**
     *
     */
    public function __destruct() {
        $this->Provider->autocommit(true);
    }
}