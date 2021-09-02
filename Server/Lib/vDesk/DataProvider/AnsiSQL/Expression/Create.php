<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression;

use vDesk\DataProvider\Expression\ICreate;
use vDesk\DataProvider\IResult;
use vDesk\DataProvider;

/**
 * Represents a MySQL compatible CREATE SQL expression.
 *
 * @package vDesk\DataProvider\AnsiSQL
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Create implements ICreate {

    /**
     * The SQL-statement of the Create.
     *
     * @var string
     */
    protected string $Statement = "CREATE ";

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
    public function PrimaryKey(string $Name, bool $Unique = false): static {
        $this->Statement .= "PRIMARY KEY ";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Index(string $Name, bool $Unique = false): static {
        $this->Statement .= ($Unique ? " UNIQUE" : "") . " INDEX " . DataProvider::EscapeField($Name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function On(string $Table, array $Fields): static {
        $Transformed = [];
        foreach($Fields as $Field => $Size) {
            $Transformed[] = \is_string($Field) ? DataProvider::EscapeField($Field) . " ({$Size})" : DataProvider::EscapeField($Size);
        }
        $this->Statement .= " ON " . DataProvider::SanitizeField($Table) . " (" . \implode(", ", $Transformed) . ")";
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
}