<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression;

use vDesk\DataProvider\Expression\IAlter;
use vDesk\DataProvider\IResult;
use vDesk\DataProvider;

/**
 * Abstract base class for AnsiSQL compatible "ALTER" Expressions.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Alter implements IAlter {

    /**
     * The SQL-statement of the Expression.
     *
     * @var string
     */
    protected string $Statement = "ALTER ";

    /**
     * The statements of the Expression.
     *
     * @var string[]
     */
    protected array $Statements = [];

    /**
     * @inheritDoc
     */
    public function Database(string $Name): static {
        $this->Statement .= "DATABASE " . DataProvider::EscapeField($Name) . " ";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Schema(string $Name): static {
        $this->Statement .= "SCHEMA " . DataProvider::SanitizeField($Name) . " ";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Table(string $Name): static {
        $this->Statement .= "TABLE " . DataProvider::SanitizeField($Name) . " ";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Drop(array $Columns, array $Indexes = []): static {
        foreach($Columns as $Column) {
            $this->Statements[] = "DROP COLUMN " . DataProvider::SanitizeField($Column);
        }
        foreach($Indexes as $Index) {
            $this->Statements[] = "DROP " . ($Index === "Primary" ? "PRIMARY KEY" : "INDEX " . DataProvider::SanitizeField($Index));
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Execute(bool $Buffered = true): IResult {
        return DataProvider::Execute((string)$this, $Buffered);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string {
        return $this->Statement . \implode(", ", $this->Statements);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(): null|string|int|float {
        return $this->Execute()->ToValue();
    }
}