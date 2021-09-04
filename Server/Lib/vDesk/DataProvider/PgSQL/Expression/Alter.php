<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

use vDesk\DataProvider\Expression\IAlter;
use vDesk\DataProvider\IResult;
use vDesk\DataProvider;

/**
 * Represents a MySQL compatible ALTER SQL expression.
 *
 * @package vDesk\DataProvider\PgSQL
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Alter extends DataProvider\AnsiSQL\Expression\Alter {

    /**
     * The table name of the Alter Expression.
     *
     * @var string
     */
    private $Table = "";

    /**
     * @inheritDoc
     */
    public function Table(string $Name): static {
        $this->Table = $Name;
        return parent::Table($Name);
    }

    /**
     * @inheritDoc
     */
    public function Add(array $Columns, array $Indexes = []): static {
        foreach($Columns as $Name => $Column) {
            $this->Statements[] = "ADD COLUMN " . Table::Field(
                    $Name,
                    $Column["Type"],
                    $Column["Size"] ?? null,
                    $Column["Nullable"] ?? false,
                    $Column["Default"] ?? "",
                    $Column["Autoincrement"] ?? false,
                    $Column["OnUpdate"] ?? null
                );
        }
        foreach($Indexes as $Name => $Index) {
            $this->Statements[] = "ADD INDEX " . Table::Index(
                    $Name,
                    $Index["Fields"],
                    $Index["Unique"] ?? false
                );
            $this->Statements[] = (new Create())->Index($Name, $Index["Unique"] ?? false)->On($Name, $Index["Fields"]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Rename(array $Columns, array $Indexes = []): static {
        foreach($Columns as $Name => $NewName) {
            $this->Statements[] = "RENAME COLUMN " . DataProvider::SanitizeField($Name) . " TO " . DataProvider::SanitizeField($NewName);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Modify(array $Columns): static {
        foreach($Columns as $Name => $Column) {
            $this->Statements[] = "ALTER COLUMN " . Table::Field(
                    $Name,
                    $Column["Type"],
                    $Column["Size"] ?? null,
                    $Column["Nullable"] ?? false,
                    $Column["Default"] ?? "",
                    $Column["Autoincrement"] ?? false,
                    $Column["OnUpdate"] ?? null
                );
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
            $this->Statements[] = "DROP INDEX " . ($Index === "Primary" ? "PRIMARY KEY" : "INDEX {$Index}");
        }
        return $this;
    }

}