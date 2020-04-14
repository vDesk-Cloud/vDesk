<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider\Expression\IDelete;
use vDesk\DataProvider\IResult;
use vDesk\DataProvider\MySQL\Expression;
use vDesk\DataProvider;

/**
 * Represents a MySQL compatible DELETE SQL expression.
 *
 * @package vDesk\DataProvider\Expression\Delete
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Delete implements IDelete {

    /**
     * The SQL-statement of the Delete\MariaDB.
     *
     * @var string
     */
    private string $Statement = "";
    
    /**
     * @inheritDoc
     */
    public function From(string $Table): self {
        $this->Statement .= "DELETE FROM {$Table} ";
        return $this;
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
    public function __invoke() {
        return $this->Execute()->ToValue();
    }
}