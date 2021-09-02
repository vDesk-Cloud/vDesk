<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression;

use vDesk\DataProvider;
use vDesk\DataProvider\AnsiSQL\Expression;
use vDesk\DataProvider\Expression\IDelete;
use vDesk\DataProvider\IResult;

/**
 * Represents a MySQL compatible DELETE SQL expression.
 *
 * @package vDesk\DataProvider\Expression\Delete
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
abstract class Delete implements IDelete {
    
    /**
     * The SQL-statement of the Delete\MariaDB.
     *
     * @var string
     */
    protected string $Statement = "";
    
    /**
     * @inheritDoc
     */
    public function From(string $Table): static {
        $this->Statement .= "DELETE FROM " . DataProvider::SanitizeField($Table) . " ";
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function Where(array ...$Conditions): static {
        $this->Statement .= "WHERE " . Expression::TransformConditions([], ...$Conditions);
        return $this;
    }

    //Implementation of IExpression.
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

    /**
     * @inheritDoc
     */
    public function __invoke(): IResult|string|null {
        return $this->Execute()->ToValue();
    }
    
}