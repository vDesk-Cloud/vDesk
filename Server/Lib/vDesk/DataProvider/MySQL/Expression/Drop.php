<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider\Expression\IDrop;
use vDesk\DataProvider\IResult;
use vDesk\DataProvider;

/**
 * Represents a MySQL compatible DROP SQL expression.
 *
 * @package vDesk\DataProvider\Expression\Drop
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Drop implements IDrop {

    /**
     * The SQL-statement of the Create\MariaDB.
     *
     * @var string
     */
    private string $Statement = "";
    
    /**
     * @inheritDoc
     */
    public function Table(string $Name, ...$Fields): self {
        $this->Statement .= "DROP TABLE {$Name}";
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function Database(string $Name): self {
        $this->Statement .= "DROP DATABASE {$Name}";
        return $this;
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