<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider\Expression\ICreate;
use vDesk\DataProvider\IResult;
use vDesk\DataProvider;

/**
 * Represents a MySQL compatible CREATE SQL expression.
 *
 * @package vDesk\DataProvider\Expression\Create
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Create implements ICreate {
    
    /**
     * The SQL-statement of the Create\MariaDB.
     *
     * @var string
     */
    private string $Statement = "";
    
    /**
     * @inheritDoc
     */
    public function Table(string $Name, array $Fields = [], array $Indexes = [], $Options = []): self {
        $Table = [];
        foreach($Fields as $FieldName => $Field) {
            $Table[] = Table::Field(
                $FieldName,
                $Field["Type"],
                $Field["Size"] ?? null,
                $Field["Collation"] ?? null,
                $Field["Nullable"] ?? false,
                \array_key_exists("Default", $Field) ? $Field["Default"] : "",
                $Field["Autoincrement"] ?? false,
                $Field["OnUpdate"] ?? null
            );
        }
        foreach($Indexes as $IndexName => $Index) {
            $Table[] = Table::Index(
                $IndexName,
                $Index["Fields"],
                $Index["Unique"] ?? false
            );
        }
        $this->Statement .= "CREATE TABLE " . DataProvider::SanitizeField($Name) . " (" . \implode(", ", $Table) . ")";
        $this->Statement .= " ENGINE=" . ($Options["Engine"] ?? "INNODB");
        $this->Statement .= " DEFAULT CHARSET=" . ($Options["Charset"] ?? "utf8mb4");
        $this->Statement .= " COLLATE=" . ($Options["Collation"] ?? "utf8mb4_unicode_ci");
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
     * @return \vDesk\DataProvider\MySQL\Expression\Create The current instance for further chaining.
     */
    public function Engine(string $Name): self {
        $this->Statement .= " ENGINE=$Name";
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