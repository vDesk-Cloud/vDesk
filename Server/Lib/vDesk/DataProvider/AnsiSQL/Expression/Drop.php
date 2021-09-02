<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression;

use vDesk\DataProvider;
use vDesk\DataProvider\Expression\IDrop;
use vDesk\DataProvider\IResult;

/**
 * Represents a AnsiSQL compatible DROP SQL expression.
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
    public function Database(string $Name): static {
        $this->Statement .= "DROP DATABASE " . DataProvider::EscapeField($Name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Schema(string $Name): static {
        $this->Statement .= "DROP SCHEMA " . DataProvider::EscapeField($Name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Table(string $Name, ...$Fields): static {
        $this->Statement .= "DROP TABLE " . DataProvider::EscapeField($Name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Index(string $Name): static {
        $this->Statement .= "DROP INDEX " . DataProvider::EscapeField($Name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function On(string $Table): static {
        $this->Statement .= "ON " . DataProvider::SanitizeField($Table);
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