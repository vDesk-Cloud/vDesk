<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider;
use vDesk\DataProvider\IResult;

/**
 * Represents a MySQL compatible CREATE SQL expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Create extends DataProvider\AnsiSQL\Expression\Create {

    /**
     * Flag indicating whether the Database method has been called.
     *
     * @var bool
     */
    private bool $Database = false;

    /**
     * @inheritDoc
     */
    public function Database(string $Name): static {
        $this->Database = true;
        return $this;
    }

    /**
     * Applies a "DATABASE"-statement to the Create Expression due to lack of schema support of MySQL.
     */
    public function Schema(string $Name): static {
        return parent::Database($Name);
    }

    /**
     * @inheritDoc
     */
    public function Table(string $Name, array $Fields = [], array $Indexes = [], $Options = []): static {
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
        $this->Statement .= "TABLE " . DataProvider::SanitizeField($Name) . " (" . \implode(", ", $Table) . ")";
        $this->Engine($Options["Engine"] ?? "INNODB");
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
    public function Engine(string $Name): static {
        $this->Statement .= " ENGINE=$Name";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Execute(bool $Buffered = true): IResult {
        if($this->Database) {
            return new DataProvider\Result(true);
        }
        return DataProvider::Execute($this->Statement, $Buffered);
    }

}