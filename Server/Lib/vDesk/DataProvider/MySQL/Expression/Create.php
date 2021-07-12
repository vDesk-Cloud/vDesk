<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider;

/**
 * Represents a MySQL compatible CREATE SQL expression.
 *
 * @package vDesk\DataProvider\Expression\Create
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Create extends DataProvider\AnsiSQL\Expression\Create {

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
                $Field["Default"] ?? "",
                $Field["Autoincrement"] ?? false,
                $Field["OnUpdate"] ?? null
            );
        }
        foreach($Indexes as $IndexName => $Index) {
            $Table[] = Table::Index($IndexName, $Index["Unique"] ?? false, $Index["Fields"]);
        }
        $this->Statement .= "CREATE TABLE " . DataProvider::SanitizeField($Name) . " (" . \implode(", ", $Table) . ")";
        $this->Statement .= " ENGINE=" . ($Options["Engine"] ?? "INNODB");
        $this->Statement .= " DEFAULT CHARSET=" . ($Options["Charset"] ?? "utf8mb4");
        $this->Statement .= " COLLATE=" . ($Options["Collation"] ?? "utf8mb4_unicode_ci");
        return $this;
    }

    /**
     * Mysql specific extension for defining storage engines.
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
     * Mysql specific extension for creating indices.
     *
     * @param string $Name   The name of the index to create.
     * @param bool   $Unique Flag indicating whether to create an unique index.
     * @param array  $Fields $Fields The fields of the index.
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Create The current instance for further chaining.
     */
    public function Index(string $Name, bool $Unique, array $Fields): self {
        $this->Statement .= "CREATE " . Table::Index($Name, $Unique, $Fields);
        return $this;
    }

}