<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression;

use vDesk\DataProvider\IResult;
use vDesk\DataProvider;
use vDesk\DataProvider\Expression\IInsert;

/**
 * Represents a MySQL compatible INSERT SQL expression.
 *
 * @package vDesk\DataProvider\Expression\Insert
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
final class Insert implements IInsert {
    
    /**
     * The SQL-statement of the Insert\MariaDB.
     *
     * @var string
     */
    private string $Statement = "";
    
    /**
     * The fields of the Insert\MariaDB.
     *
     * @var string[]
     */
    private array $Fields = [];
    
    /**
     * @inheritDoc
     */
    public function Execute($Buffered = true): IResult {
        return DataProvider::Execute($this->Statement, $Buffered);
    }
    
    /**
     * @inheritDoc
     */
    public function __toString(): string {
        return $this->Statement;
    }
    
    /**
     * @inheritDoc
     */
    public function Into(string $Table, array $Fields = null): self {
        $this->Statement .= "INSERT INTO {$Table} ";
        $this->Fields    = $Fields;
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function Values(array $Values, array ...$Multiple): self {
        if(\count($this->Fields) > 0) {
            $this->Statement .= "("
                . \implode(
                    ", ",
                    \array_map(
                        static fn($Field): string => DataProvider::EscapeField($Field),
                        $this->Fields
                    )
                )
                . ") VALUES ("
                . \implode(
                    ", ",
                    \array_map(
                        static fn($Value) => DataProvider::Sanitize($Value),
                        \array_values($Values)
                    )
                )
                . ")";
            foreach($Multiple as $AdditionalValues) {
                $this->Statement .= ", ("
                    . \implode(
                        ", ",
                        \array_map(
                            static fn($Value) => DataProvider::Sanitize($Value),
                            \array_values($AdditionalValues)
                        )
                    )
                    . ")";
            }
            
        } else {
            $this->Statement .= "("
                . \implode(
                    ", ",
                    \array_map(
                        static fn($Field): string => DataProvider::EscapeField($Field),
                        \array_keys($Values)
                    )
                )
                . ") VALUES ("
                . \implode(
                    ", ",
                    \array_map(
                        static fn($Value) => DataProvider::Sanitize($Value),
                        \array_values($Values)
                    )
                )
                . ")";
        }
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function __invoke(): IResult|string|null {
        return $this->Execute()->ToValue();
    }
    
    /**
     * @inheritDoc
     */
    public function ID(): int {
        $this->Execute();
        return DataProvider::LastInsertID();
    }
    
}