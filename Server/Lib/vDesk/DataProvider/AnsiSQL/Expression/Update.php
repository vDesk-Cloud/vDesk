<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression;

use vDesk\DataProvider\Expression\IUpdate;
use vDesk\DataProvider\AnsiSQL\Expression;
use vDesk\DataProvider;
use vDesk\DataProvider\IResult;

/**
 * Abstract base class for AnsiSQL compatible "UPDATE" Expressions.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
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
        $this->Statement .= "UPDATE " . DataProvider::SanitizeField($Table) . " ";
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
    public function __invoke(): null|string|int|float {
        return $this->Execute()->ToValue();
    }

    /**
     * @inheritDoc
     */
    public function Execute(bool $Buffered = true): IResult {
        return DataProvider::Execute($this->Statement, $Buffered);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string {
        return $this->Statement;
    }
    
}