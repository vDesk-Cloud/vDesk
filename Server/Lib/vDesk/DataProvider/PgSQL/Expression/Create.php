<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

use vDesk\DataProvider;

/**
 * Represents a PgSQL compatible "CREATE" Expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Create extends DataProvider\AnsiSQL\Expression\Create {

    /**
     * @inheritDoc
     */
    public function Table(string $Name, array $Fields = [], array $Indexes = [], $Options = []): static {
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
            if($IndexName === "Primary") {
                $Table[] = Table::Index($IndexName, $Index["Unique"] ?? false, $Index["Fields"]);
            }else{
                $Indices[] = (new static())->Index($IndexName, $Index["Unique"] ?? false)->On($Name, $Index["Fields"]);
            }
        }

        $this->Statement .= "TABLE " . DataProvider::SanitizeField($Name) . " (" . \implode(", ", $Table) . ");" . \PHP_EOL;
        $this->Statement .= \implode(";" . \PHP_EOL, $Indices);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function On(string $Table, array $Fields): static {
        $Transformed = [];
        foreach($Fields as $Field => $Size) {
            $Transformed[] = \is_string($Field) ? DataProvider::EscapeField($Field) : DataProvider::EscapeField($Size);
        }
        $this->Statement .= " ON " . DataProvider::SanitizeField($Table) . " (" . \implode(", ", $Transformed) . ")";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Database(string $Name): static {
        $this->Statement .= "DATABASE " . DataProvider::EscapeField($Name) . " WITH ENCODING 'UTF8'";
        return $this;
    }

}