<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression;

use vDesk\DataProvider\Expression\IAlter;
use vDesk\DataProvider\IResult;
use vDesk\DataProvider;

/**
 * Represents a MySQL compatible ALTER SQL expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Alter implements IAlter {

    /**
     * The SQL-statement of the Create\MariaDB.
     *
     * @var string
     */
    private string $Statement = "";

    /**
     * The statements of the Expression.
     *
     * @var string[]
     */
    private array $Statements = [];

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
        $this->Statement .= "SCHEMA " . DataProvider::EscapeField($Name) . " ";
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function Table(string $Name): static {
        $this->Statement .= "ALTER TABLE " . DataProvider::SanitizeField($Name) . " ";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Add(array $Columns, array $Indexes = []): static {
        foreach($Columns as $Name => $Column) {
            $this->Statements[] = "ADD " . Table::Field(
                    $Name,
                    $Column["Type"],
                    $Column["Nullable"] ?? false,
                    $Column["Autoincrement"] ?? false,
                    $Column["Default"] ?? "",
                    $Column["Collation"] ?? null,
                    $Column["Size"] ?? null,
                    $Column["OnUpdate"] ?? null
                );
        }
        foreach($Indexes as $Name => $Index) {
            $this->Statements[] = "ADD " . Table::Index($Name, $Index["Unique"] ?? false, $Index["Fields"]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Rename(string $Name): static {
        $this->Statements[] = "RENAME " . DataProvider::SanitizeField($Name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Modify(array $Columns, array $Indexes = []): static {
        foreach($Columns as $Name => $Column) {
            if(\is_array($Column)) {
                $this->Statements[] = "MODIFY COLUMN " . Table::Field(
                        $Name,
                        $Column["Type"],
                        $Column["Nullable"] ?? false,
                        $Column["Autoincrement"] ?? false,
                        $Column["Default"] ?? "",
                        $Column["Collation"] ?? null,
                        $Column["Size"] ?? null,
                        $Column["OnUpdate"] ?? null
                    );
            } else {
                $this->Statements[] = "RENAME COLUMN " . DataProvider::SanitizeField($Name) . " TO " . DataProvider::SanitizeField($Column);
            }
        }
        foreach($Indexes as $Old => $New) {
            $this->Statements[] = "RENAME INDEX " . DataProvider::SanitizeField($Old) . " TO " . DataProvider::SanitizeField($New);
        }
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
            $this->Statements[] = "DROP " . ($Index === "Primary" ? "PRIMARY KEY" : "INDEX {$Index}");
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
    public function __invoke(): IResult|string|null {
        return $this->Execute()->ToValue();
    }

}