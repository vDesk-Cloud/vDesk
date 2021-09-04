<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression;

use vDesk\DataProvider\Expression\IDrop;
use vDesk\DataProvider\IResult;
use vDesk\DataProvider;

/**
 * Represents a MySQL compatible DROP SQL expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
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
    public function __toString(): string {
        return $this->Statement;
    }
    
    /**
     * @inheritDoc
     */
    public function __invoke(): IResult|string|null {
        return $this->Execute()->ToValue();
    }

    public function Schema(string $Name): IDrop {
        // TODO: Implement Schema() method.
    }

    public function Index(string $Name): IDrop {
        // TODO: Implement Index() method.
    }

    public function On(string $Table): IDrop {
        // TODO: Implement On() method.
    }
}