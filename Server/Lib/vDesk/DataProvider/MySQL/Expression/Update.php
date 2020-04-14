<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider\Expression\IUpdate;
use vDesk\DataProvider\IResult;
use vDesk\DataProvider\MySQL\Expression;
use vDesk\DataProvider;

/**
 * Represents a MySQL compatible UPDATE SQL expression.
 *
 * @package vDesk\DataProvider\Expression\Update
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Update implements IUpdate {
    
    /**
     * The SQL-statement of the Update.
     *
     * @var string
     */
    private string $Statement = "";
    
    /**
     * Initializes a new instance of the Update class.
     *
     * @param string $Table Initializes the Update with the specified table.
     */
    public function __construct(string $Table) {
        $this->Statement .= "UPDATE {$Table} ";
    }
    
    /**
     * @inheritDoc
     */
    public function Set(array $Fields): self {
        $Statements = [];
        foreach($Fields as $Field => $Value) {
            $Statements[] = DataProvider::EscapeField($Field) . " = " . DataProvider::Sanitize($Value);
        }
        $this->Statement .= " SET " . \implode(", ", $Statements) . " ";
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function SetIf(array $Fields): self {
        $Statements = [];
        foreach($Fields as $Field => $Condition) {
            if((bool)\key($Condition)) {
                $Statements[] = DataProvider::EscapeField($Field) . " = " . DataProvider::Sanitize(\current($Condition));
            }
        }
        $this->Statement .= " SET " . \implode(", ", $Statements) . " ";
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function Execute($Buffered = true): IResult {
        return DataProvider::Execute($this->Statement, $Buffered);
    }
    
    /**
     * @inheritDoc
     */
    public function __toString() {
        return $this->Statement;
    }
    
    /**
     * @inheritDoc
     */
    public function Where(array ...$Conditions): self {
        $this->Statement .= "WHERE " . Expression::TransformConditions([], ...$Conditions);
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function __invoke() {
        return $this->Execute()->ToValue();
    }
    
}