<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider;
use vDesk\DataProvider\IResult;

/**
 * Represents a MySQL compatible SELECT SQL expression.
 *
 * @package vDesk\DataProvider\Expression\Select
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Select extends DataProvider\AnsiSQL\Expression\Select {

    //MySQL's SELECT is (mostly) AnsiSQL conform.
    /**
     * @inheritDoc
     */
    public function Execute(bool $Buffered = true): IResult {
        return DataProvider::Execute($this->Statement, $Buffered);
    }

    /**
     * @inheritDoc
     */
    public function RightJoin(string $Table, string $Alias = null): static {
        return $this->Join("RIGHT", $Table, $Alias);
    }

    /**
     * @inheritDoc
     */
    public function LeftJoin(string $Table, string $Alias = null): static {
        return $this->Join("LEFT", $Table, $Alias);
    }

    /**
     * @inheritDoc
     */
    public function FullJoin(string $Table, string $Alias = null): static {
        //FULL OUTER JOIN shim.
        $Right = clone $this;
        $this->LeftJoin($Table, $Alias);
        return $this->Union($Right->RightJoin($Table, $Alias));
    }

}