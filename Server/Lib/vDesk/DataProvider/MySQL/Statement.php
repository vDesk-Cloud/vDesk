<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL;

use vDesk\DataProvider\IPreparedStatement;
use vDesk\DataProvider\SQLException;

/**
 * Class MariaDB represents ...
 *
 * @package vDesk\DataProvider\Statement
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Statement implements IPreparedStatement {

    /**
     * The statement of the MariaDB.
     *
     * @var \mysqli_stmt|null
     */
    private ?\mysqli_stmt $Statement;

    /**
     *Flag indicating whether the MariaDB is buffered.
     *
     * @var bool
     */
    private bool $Buffered;

    /**
     * Initializes a new instance of the MariaDB class.
     *
     * @param \mysqli_stmt $Statement Initializes the IPreparedStatement with the specified prepared-statement.
     * @param bool         $Buffered  Determines whether the result-set will be buffered.
     */
    public function __construct(\mysqli_stmt $Statement, bool $Buffered = true) {
        $this->Statement = $Statement;
        $this->Buffered  = $Buffered;
    }
    
    /**
     * @param string $Types
     * @param mixed  ...$Values
     *
     * @return \vDesk\DataProvider\MySQL\Statement The current instance for further chaining.
     */
    public function Apply(string $Types, ...$Values): self {
        if(!$this->Statement->bind_param($Types, ...$Values)) {
            throw new \InvalidArgumentException();
        }
        return $this;
    }
    
    /**
     * Executes the MariaDB against the database.
     *
     * @return \vDesk\DataProvider\MySQL\Result The result of the execution.
     */
    public function Execute(): Result {
        if(!$this->Statement->execute()) {
            throw new SQLException($this->Statement->error, $this->Statement->errno);
        }
        return new Result($this->Statement->get_result());
    }
}