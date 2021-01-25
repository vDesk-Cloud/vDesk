<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider\Expression\IAlter;
use vDesk\DataProvider\IResult;
use vDesk\DataProvider;

/**
 * Represents a MySQL compatible ALTER SQL expression.
 *
 * @package vDesk\DataProvider\Expression\Alter
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
    private array  $Statements = [];
    
    /**
     * @inheritDoc
     */
    public function Table(string $Name): self {
        $this->Statement .= "ALTER TABLE " . DataProvider::SanitizeField($Name) . " ";
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
     * Applies a storage engine to the Create\MariaDB.
     *
     * @param string $Name The name of the storage engine to set.
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Alter The current instance for further chaining.
     */
    public function Engine(string $Name): self {
        $this->Statement .= " ENGINE=$Name";
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
                    $Column["Collation"] ?? null,
                    $Column["Nullable"] ?? false,
                    \array_key_exists("Default", $Column) ? $Column["Default"] : "",
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
                    $Column["Collation"] ?? null,
                    $Column["Nullable"] ?? false,
                    \array_key_exists("Default", $Column) ? $Column["Default"] : "",
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
    
}