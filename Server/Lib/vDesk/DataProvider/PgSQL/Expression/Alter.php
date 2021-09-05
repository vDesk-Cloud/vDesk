<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

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
            //$this->Statements[] = "ADD " . Table::Index($Name, $Index["Fields"], $Index["Unique"] ?? false);
            $this->Statements[] = (new Create())->Index($Name, $Index["Unique"] ?? false)->On($Name, $Index["Fields"]);
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
            $this->Statements[] = "RENAME INDEX " . DataProvider::SanitizeField($Old) . " TO " . DataProvider::SanitizeField($New);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Rename(string $Name): static {
        $this->Statement .= "RENAME TO " . DataProvider::EscapeField($Name);
        return $this;
    }

}