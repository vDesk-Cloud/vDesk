<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

use vDesk\DataProvider;

/**
 * Represents a PgSQL compatible "ALTER" Expression.
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
    private string $Table = "";

    /**
     * The indexes of the Alter Expression
     *
     * @var array
     */
    private array $Indexes = [];

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
            $this->Indexes[] = DataProvider\Expression::Create()
                                                      ->Index($Name, $Index["Unique"] ?? false)
                                                      ->On($this->Table, $Index["Fields"]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Modify(array $Columns, array $Indexes = []): static {
        foreach($Columns as $Name => $Column) {
            if(\is_array($Column)) {
                $this->Statements[] = "ALTER COLUMN " . Table::Field(
                        $Name,
                        $Column["Type"],
                        $Column["Size"] ?? null,
                        $Column["Nullable"] ?? false,
                        $Column["Default"] ?? "",
                        $Column["Autoincrement"] ?? false,
                        $Column["OnUpdate"] ?? null
                    );
            } else {
                $this->Statements[] = "RENAME COLUMN " . DataProvider::SanitizeField($Name) . " TO " . DataProvider::SanitizeField($Column);
            }
        }
        foreach($Indexes as $Old => $New) {
            $this->Indexes[] = "ALTER INDEX " . DataProvider::SanitizeField($Old) . " RENAME TO " . DataProvider::SanitizeField($New);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Drop(array $Columns, array $Indexes = []): static {
        parent::Drop($Columns);
        foreach($Indexes as $Index) {
            $this->Indexes[] = DataProvider\Expression::Drop()
                                                      ->Index($Index)
                                                      ->On($this->Table);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string {
        if(\count($this->Indexes) > 0) {
            return parent::__toString() . "; " . \implode("; ", $this->Indexes);
        }
        return parent::__toString();
    }

    /**
     * @inheritDoc
     */
    public function Rename(string $Name): static {
        $this->Statement .= "RENAME TO " . DataProvider::EscapeField($Name);
        return $this;
    }

}