<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider\IResult;
use vDesk\DataProvider;

/**
 * Represents a MySQL compatible "ALTER" Expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Alter extends DataProvider\AnsiSQL\Expression\Alter {

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
        $this->Database  = true;
        //$this->Statement .= "ALTER DATABASE " . DataProvider::SanitizeField($Name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Schema(string $Name): static {
        $this->Statement .= "DATABASE " . DataProvider::SanitizeField($Name) . " ";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Rename(string $Name): static {
        $this->Statements[] = "RENAME " . DataProvider::EscapeField($Name);
        return $this;
    }

    /**
     * Applies a storage engine to the Alter.
     *
     * @param string $Name The name of the storage engine to set.
     *
     * @return \vDesk\DataProvider\MySQL\Expression\Alter The current instance for further chaining.
     */
    public function Engine(string $Name): static {
        $this->Statement .= " ENGINE=$Name";
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
                    $Column["Collation"] ?? null,
                    $Column["Size"] ?? null,
                    $Column["OnUpdate"] ?? null
                );
        }
        foreach($Indexes as $Name => $Index) {
            $this->Statements[] = "ADD " . Table::Index($Name, $Index["Unique"] ?? false, $Index["Fields"]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Modify(array $Columns, array $Indexes = []): static {
        foreach($Columns as $Name => $Column) {
            if(\is_array($Column)) {
                $this->Statements[] = "MODIFY COLUMN " . Table::Field(
                        $Name,
                        $Column["Type"],
                        $Column["Nullable"] ?? false,
                        $Column["Autoincrement"] ?? false,
                        $Column["Default"] ?? "",
                        $Column["Collation"] ?? null,
                        $Column["Size"] ?? null,
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
    public function Execute(bool $Buffered = true): IResult {
        if($this->Database) {
            return new DataProvider\Result(true);
        }
        return DataProvider::Execute($this->Statement, $Buffered);
    }

}