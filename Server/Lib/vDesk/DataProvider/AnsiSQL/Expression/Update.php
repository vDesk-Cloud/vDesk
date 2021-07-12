<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression;

use vDesk\DataProvider\Expression\IUpdate;
use vDesk\DataProvider\AnsiSQL\Expression;
use vDesk\DataProvider;
use vDesk\DataProvider\IResult;

/**
 * Represents an AnsiSQL compatible UPDATE SQL expression.
 *
 * @package vDesk\DataProvider\Expression\Update
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
abstract class Update implements IUpdate {
    
    /**
     * The SQL-statement of the Update.
     *
     * @var string
     */
    protected string $Statement = "";
    
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
    public function Where(array ...$Conditions): self {
        $this->Statement .= "WHERE " . Expression::TransformConditions([], ...$Conditions);
        return $this;
    }

    //Implementation of IExpression.
    /**
     * @inheritDoc
     */
    public function __invoke(): IResult|string|null {
        return $this->Execute()->ToValue();
    }

    /**
     * @inheritDoc
     */
    public function Execute(): IResult {
        return DataProvider::Execute($this->Statement);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string {
        return $this->Statement;
    }
    
}