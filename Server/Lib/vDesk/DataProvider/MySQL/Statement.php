<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL;

use vDesk\DataProvider\IPreparedStatement;
use vDesk\DataProvider\SQLException;
use vDesk\IO\FileInfo;
use vDesk\IO\FileStream;
use vDesk\Struct\Type;

/**
 * Class Statement represents ...
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Statement implements IPreparedStatement {

    /**
     * Initializes a new instance of the Statement class.
     *
     * @param null|\mysqli_stmt $Statement Initializes the Statement with the specified prepared-statement.
     * @param bool              $Buffered  Determines whether the result-set will be buffered.
     */
    public function __construct(protected ?\mysqli_stmt $Statement = null, public bool $Buffered = true) { }
    
    /**
     * Applies a set of values to the Statement.
     *
     * @param mixed  ...$Values The values to apply.
     *
     * @return \vDesk\DataProvider\MySQL\Statement The current instance for further chaining.
     */
    public function Apply(mixed ...$Values): self {
        $Types = "";
        foreach($Values as $Value){
            $Types .= match (Type::Of($Value)) {
                Type::Int => "i",
                Type::Float => "d",
                FileStream::class, FileInfo::class => "b",
                default => "s"
            };
        }
        if(!$this->Statement->bind_param($Types, ...$Values)) {
            throw new \InvalidArgumentException();
        }
        return $this;
    }

    /**
     * Executes the Statement against the database.
     *
     * @return \vDesk\DataProvider\MySQL\Result The result of the execution.
     * @throws \vDesk\DataProvider\SQLException
     */
    public function Execute(): Result {
        if(!$this->Statement->execute()) {
            throw new SQLException($this->Statement->error, $this->Statement->errno);
        }
        return new Result($this->Statement->get_result());
    }
}