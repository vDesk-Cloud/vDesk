<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

use vDesk\DataProvider;
use vDesk\DataProvider\Type;

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
    public function Rename(string $Name): static {
        $Path            = \explode(DataProvider::$Separator, $Name);
        $this->Indexes[] = $this->Statement . " RENAME TO " . DataProvider::EscapeField(\array_pop($Path));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Add(array $Columns, array $Indexes = []): static {
        foreach($Columns as $Name => $Column) {
            $this->Statements[] = "ADD COLUMN " . Table::Field(
                    $Name,
                    $Column["Type"],
                    $Column["Nullable"] ?? false,
                    $Column["Autoincrement"] ?? false,
                    $Column["Default"] ?? "",
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
        //@todo Blame PostgreSQL developers.
        foreach($Columns as $Name => $Column) {
            if(\is_array($Column)) {
                if($Column["Autoincrement"] ?? false) {
                    $Type = match (Table::Types[$Column["Type"] & ~Type::Unsigned]) {
                        Table::Types[Type::SmallInt], Table::Types[Type::TinyInt] => "SMALLSERIAL",
                        Table::Types[Type::Int] => "SERIAL",
                        Table::Types[Type::BigInt] => "BIGSERIAL"
                    };
                } else {
                    $Type = Table::Types[$Column["Type"] & ~DataProvider\Type::Unsigned];
                }
                $this->Statements[] = "ALTER COLUMN " . DataProvider::SanitizeField($Name) . " TYPE {$Type}";
                if(isset($Column["Nullable"])) {
                    $this->Statements[] = "ALTER COLUMN " . DataProvider::SanitizeField($Name) . ($Column["Nullable"] ? " SET NOT NULL" : " DROP NOT NULL");
                }
                if(isset($Column["Default"])) {
                    $this->Statements[] = "ALTER COLUMN " . DataProvider::SanitizeField($Name) . " SET DEFAULT " . DataProvider::Sanitize($Column["Default"]);
                }
            } else {
                $this->Statements[] = "ALTER COLUMN " . DataProvider::SanitizeField($Name) . " RENAME TO " . DataProvider::SanitizeField($Column);
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
        foreach($Indexes as $Index) {
            $this->Indexes[] = DataProvider\Expression::Drop()
                                                      ->Index($Index)
                                                      ->On($this->Table);
        }
        return parent::Drop($Columns);
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

}