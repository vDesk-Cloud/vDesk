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

    //MySQL's SELECT is ANSI conform.
    /**
     * @inheritDoc
     */
    public function Execute(bool $Buffered = true): IResult {
        return DataProvider::Execute($this->Statement, $Buffered);
    }

}