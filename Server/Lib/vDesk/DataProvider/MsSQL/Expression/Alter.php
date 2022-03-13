<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression;

use vDesk\Configuration\Settings;
use vDesk\DataProvider;

/**
 * Represents a MsSQL compatible "ALTER" Expression.
 *
 * @package vDesk\DataProvider
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
     * Flag indicating whether the Expression alters a schema.
     *
     * @var bool
     */
    private bool $Schema = false;

    /**
     * @inheritDoc
     */
    public function Schema(string $Name): static {
        $this->Schema = true;
        return parent::Schema($Name);
    }

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
            $this->Statements[] = (new static())->Table($this->Table)
                                                ->AddColumn($Name, $Column);
        }
        foreach($Indexes as $Name => $Index) {
            $this->Statements[] = DataProvider\Expression::Create()
                                                         ->Index($Name, $Index["Unique"] ?? false)
                                                         ->On($this->Table, $Index["Fields"]);
        }
        return $this;
    }


    /**
     * Applies an "ADD (COLUMN)" statement to the Expression.
     *
     * @param string $Name   The name of the column.
     * @param array  $Column The definition of the column.
     *
     * @return $this The current instance for further chaining.
     */
    protected function AddColumn(string $Name, array $Column): static {
        $this->Statement .= "ADD " . Table::Field(
                $Name,
                $Column["Type"],
                $Column["Nullable"] ?? false,
                $Column["Autoincrement"] ?? false,
                $Column["Default"] ?? "",
                $Column["Collation"] ?? null,
                $Column["Size"] ?? null,
                $Column["OnUpdate"] ?? null
            );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Rename(string $Name): static {
        if($this->Schema) {
            $this->Statement .= "TRANSFER " . (Settings::$Local["DataProvider"]["Database"] ?? "dbo") . DataProvider\MsSQL\Provider::Separator . DataProvider::EscapeField($Name);
        } else {
            $this->Statement = "EXECUTE sp_rename " . DataProvider::Sanitize($this->Table)
                               . ", " . DataProvider::Sanitize($Name);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Modify(array $Columns, array $Indexes = []): static {
        foreach($Columns as $Name => $Column) {
            if(\is_array($Column)) {
                $this->Statements[] = (new static())->Table($this->Table)
                                                    ->AlterColumn($Name, $Column);
            } else {
                $this->Statements[] = (new static())->Table($this->Table)
                                                    ->RenameColumn($Name, $Column);
            }
        }
        //Seriously Microsoft...
        foreach($Indexes as $Old => $New) {
            $this->Statements[] = (new static())->Table($this->Table)
                                                ->RenameIndex($Old, $New);
        }
        return $this;
    }

    /**
     * Applies an "ALTER COLUMN" statement to the Expression.
     *
     * @param string $Name       The name of the column to alter.
     * @param array  $Definition The new column definition to set.
     *
     * @return $this The current instance for further chaining.
     */
    protected function AlterColumn(string $Name, array $Definition): static {
        //@todo How to preserve indices?!?
        $this->Statement .= "ALTER COLUMN " . Table::Field(
                $Name,
                $Definition["Type"],
                $Definition["Nullable"] ?? false,
                $Definition["Autoincrement"] ?? false,
                "",
                $Definition["Collation"] ?? null,
                $Definition["Size"] ?? null,
                $Definition["OnUpdate"] ?? null
            );
        if(isset($Definition["Default"])) {
            $this->Statements[] = $this->Statement;
            if($Definition["Default"] !== "") {
                $this->Statements[] = (new static())->Table($this->Table) . " ADD CONSTRAINT DF" . DataProvider::EscapeField($Name)
                                      . " DEFAULT " . DataProvider::Sanitize($Definition["Default"])
                                      . " FOR " . DataProvider::EscapeField($Name);
            } else {
                $this->Statements[] = (new static())->Table($this->Table) . " DROP CONSTRAINT DF" . DataProvider::EscapeField($Name);
            }
        }
        return $this;
    }

    /**
     * Calls the "sp_rename" stored procedure to rename a column.
     *
     * @param string $Old The name of the column to rename.
     * @param string $New The new name of the column.
     *
     * @return $this The current instance for further chaining.
     */
    protected function RenameColumn(string $Old, string $New): static {
        $this->Statement = "EXECUTE sp_rename " . DataProvider::Sanitize($this->Table . DataProvider\MsSQL\Provider::Separator . $Old)
                           . ", " . DataProvider::Sanitize($New)
                           . ", 'COLUMN'";
        return $this;
    }

    /**
     * Calls the "sp_rename" stored procedure to rename a index.
     *
     * @param string $Old The name of the column to rename.
     * @param string $New The new name of the column.
     *
     * @return $this The current instance for further chaining.
     */
    protected function RenameIndex(string $Old, string $New): static {
        $this->Statement = "EXECUTE sp_rename " . DataProvider::Sanitize($this->Table . DataProvider\MsSQL\Provider::Separator . $Old)
                           . ", " . DataProvider::Sanitize($New)
                           . ", 'INDEX'";
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function Drop(array $Columns, array $Indexes = []): static {
        $Drop = [];
        foreach($Indexes as $Index) {
            $Drop[] = DataProvider\Expression::Drop()
                                             ->Index($Index)
                                             ->On($this->Table);
        }
        foreach($Columns as $Column) {
            $Drop[] = (new static())->Table($this->Table)
                                    ->DropColumn($Column);
        }
        $this->Statements = [...$Drop, ...$this->Statements];
        return $this;
    }

    /**
     * Applies an "DROP (COLUMN)" statement to the Expression.
     *
     * @param string $Name The name of the column to drop.
     *
     * @return $this The current instance for further chaining.
     */
    protected function DropColumn(string $Name): static {
        $this->Statement .= "DROP COLUMN " . DataProvider::EscapeField($Name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string {
        if(\count($this->Statements) > 0) {
            return \implode("; \r\n\r\n", $this->Statements);
        }
        return $this->Statement;
    }

}