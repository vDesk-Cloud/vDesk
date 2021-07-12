<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression;

use vDesk\DataProvider;
use vDesk\DataProvider\Expression\IDrop;
use vDesk\DataProvider\IResult;

/**
 * Represents a MySQL compatible DROP SQL expression.
 *
 * @package vDesk\DataProvider\Expression\Drop
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Drop implements IDrop {
    
    /**
     * The SQL-statement of the Drop.
     *
     * @var string
     */
    protected string $Statement = "";
    
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
    public function Index(string $Name): IDrop {
        $this->Statement .= "DROP INDEX {$Name}";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function On(string $Table): self {
        $this->Statement .= "ON {$Table}";
        return $this;
    }

    //Implementation of IExpression.
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

    /**
     * @inheritDoc
     */
    public function __invoke(): IResult|string|null {
        return $this->Execute()->ToValue();
    }

}