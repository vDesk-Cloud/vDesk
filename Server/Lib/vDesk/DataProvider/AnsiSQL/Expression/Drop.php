<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression;

use vDesk\DataProvider;
use vDesk\DataProvider\Expression\IDrop;
use vDesk\DataProvider\IResult;

/**
 * Abstract base class for AnsiSQL compatible "DROP" Expressions.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Drop implements IDrop {

    /**
     * The SQL-statement of the Drop.
     *
     * @var string
     */
    protected string $Statement = "DROP ";

    /**
     * @inheritDoc
     */
    public function Database(string $Name): static {
        $this->Statement .= "DATABASE " . DataProvider::EscapeField($Name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Schema(string $Name): static {
        $this->Statement .= "SCHEMA " . DataProvider::EscapeField($Name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Table(string $Name): static {
        $this->Statement .= "TABLE " . DataProvider::EscapeField($Name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Index(string $Name): static {
        $this->Statement .= "INDEX " . DataProvider::EscapeField($Name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function On(string $Table): static {
        $this->Statement .= " ON " . DataProvider::SanitizeField($Table);
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
    public function __invoke(): null|string|int|float {
        return $this->Execute()->ToValue();
    }

}