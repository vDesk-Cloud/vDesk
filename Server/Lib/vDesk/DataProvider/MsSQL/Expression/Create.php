<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression;

use vDesk\DataProvider;
use vDesk\DataProvider\Collation;

/**
 * Represents a MsSQL compatible "CREATE" Expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Create extends DataProvider\AnsiSQL\Expression\Create {

    /**
     * @inheritDoc
     */
    public function Table(string $Name, array $Fields = [], array $Indexes = [], $Options = []): static {
        $Table = [];
        foreach($Fields as $FieldName => $Field) {
            $Table[] = Table::Field(
                $FieldName,
                $Field["Type"],
                $Field["Nullable"] ?? false,
                $Field["Autoincrement"] ?? false,
                $Field["Default"] ?? "",
                $Field["Collation"] ?? null,
                $Field["Size"] ?? null,
                $Field["OnUpdate"] ?? null
            );
        }

        //Create indices.
        $Indices = [];
        foreach($Indexes as $IndexName => $Index) {
            if($IndexName === "Primary") {
                $Table[] = Table::Index($IndexName, $Index["Unique"] ?? false, $Index["Fields"]);
            } else {
                $Indices[] = (new static())->Index($IndexName, $Index["Unique"] ?? false)->On($Name, $Index["Fields"]);
            }
        }

        $this->Statement .= "TABLE " . DataProvider::SanitizeField($Name) . " (" . \implode(", ", $Table) . "); ";
        $this->Statement .= \implode("; ", $Indices);
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
        $this->Statement .= "DATABASE {$Name} COLLATE " . Table::Collations[Collation::UTF8];
        return $this;
    }

}