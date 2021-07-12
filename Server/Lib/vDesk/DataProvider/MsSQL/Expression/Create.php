<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression;

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
        $Indices = [];
        foreach($Indexes as $IndexName => $Index) {
            if($IndexName === "Primary" || (isset($Index["Unique"]) && $Index["Unique"])) {
                $Table[] = Table::Index($IndexName, $Index["Fields"], $Index["Unique"] ?? false);
            }
            $Indices[] = Table::Index($IndexName, $Index["Fields"]);
        }
        $this->Statement .= "CREATE TABLE " . DataProvider::SanitizeField($Name) . " (" . \implode(", ", $Table) . ")";
        $this->Statement .= \implode(\PHP_EOL, $Indices);
        return $this;
    }

    /**
     * Postgres specific extension for creating indices.
     *
     * @param string $Table
     * @param        ...$Fields
     *
     * @return $this
     */
    public function Index(string $Table, ...$Fields): self {

    }

    /**
     * @inheritDoc
     */
    public function Database(string $Name): self {
        $this->Statement .= "CREATE DATABASE {$Name} COLLATE ENCODING 'UTF8'";
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