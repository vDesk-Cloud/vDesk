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
     * The table name of the Alter Expression.
     *
     * @var string
     */
    private       $Table      = "";

    /**
     * @inheritDoc
     */
    public function Table(string $Name): self {
        $this->Statement .= "ALTER TABLE " . DataProvider::SanitizeField($Name) . " ";
        $this->Table = $Name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Database(string $Name): self {
        $this->Statement .= "CREATE DATABASE $Name";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Add(array $Columns, array $Indexes = []): self {
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
            $this->Statements[] = (new Create())->Index($Name, $Index["Unique"] ?? false, $Index["Fields"])->On($Name);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Rename(array $Columns): self {
        foreach($Columns as $Name => $NewName) {
            $this->Statements[] = "RENAME COLUMN " . DataProvider::SanitizeField($Name) . " TO " . DataProvider::SanitizeField($NewName);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Modify(array $Columns, array $Indexes = []): self {
        foreach($Columns as $Name => $Column) {
            $this->Statements[] = "MODIFY COLUMN " . Table::Field(
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
            $this->Statements[] = "MODIFY INDEX " . Table::Index(
                    $Name,
                    $Index["Fields"],
                    $Index["Unique"] ?? false
                );
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Drop(array $Columns, array $Indexes = []): self {
        foreach($Columns as $Column) {
            $this->Statements[] = "DROP COLUMN " . DataProvider::SanitizeField($Column);
        }
        foreach($Indexes as $Index) {
            $this->Statements[] = "DROP INDEX " . ($Index === "Primary" ? "PRIMARY KEY" : "INDEX {$Index}");
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

    public function Schema(string $Name): IAlter {
        // TODO: Implement Schema() method.
    }

    public function Column(array $Columns, array $Indexes = []): IAlter {
        // TODO: Implement Column() method.
    }

    public function Index(array $Columns, array $Indexes = []): IAlter {
        // TODO: Implement Index() method.
    }
}