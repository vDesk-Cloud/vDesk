<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

use vDesk\DataProvider;

/**
 * Represents a PgSQL compatible CREATE SQL expression.
 *
 * @package vDesk\DataProvider\PgSQL
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Create extends DataProvider\AnsiSQL\Expression\Create {

    /**
     * @inheritDoc
     */
    public function Table(string $Name, array $Fields = [], array $Indexes = [], $Options = []): self {
        //Create table statement.
        $Table = [];
        foreach($Fields as $FieldName => $Field) {
            $Table[] = Table::Field(
                $FieldName,
                $Field["Type"],
                $Field["Size"] ?? null,
                $Field["Nullable"] ?? false,
                $Field["Default"] ?? "",
                $Field["Autoincrement"] ?? false,
                $Field["OnUpdate"] ?? null
            );
        }

        //Create indices.
        $Indices = [];
        foreach($Indexes as $IndexName => $Index) {
            if($IndexName === "Primary" || (isset($Index["Unique"]) && $Index["Unique"])) {
                $Table[] = Table::Index($IndexName, $Index["Unique"] ?? false, $Index["Fields"]);
            }else{
                $Indices[] = (new static())->Index($IndexName, $Index["Unique"] ?? false, $Index["Fields"])->On($Name);
            }
        }

        $this->Statement .= "CREATE TABLE " . DataProvider::SanitizeField($Name) . " (" . \implode(", ", $Table) . ");" . \PHP_EOL;
        $this->Statement .= \implode(";" . \PHP_EOL, $Indices);
        return $this;
    }

    /**
     * Postgres specific extension for creating indices.
     *
     * @param string $Name   The name of the index to create.
     * @param bool   $Unique Flag indicating whether to create an unique index.
     * @param array  $Fields $Fields The fields of the index.
     *
     * @return \vDesk\DataProvider\PgSQL\Expression\Create The current instance for further chaining.
     */
    public function Index(string $Name, bool $Unique, array $Fields): self {
        $this->Statement .= "CREATE " . Table::Index($Name, $Unique, $Fields);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Database(string $Name): self {
        $this->Statement .= "CREATE DATABASE " . DataProvider::EscapeField($Name) . " WITH ENCODING 'UTF8'";
        return $this;
    }

}