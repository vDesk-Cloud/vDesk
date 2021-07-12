<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider\IResult;
use vDesk\DataProvider;

/**
 * Represents a MySQL compatible DELETE SQL expression.
 *
 * @package vDesk\DataProvider\Expression\Delete
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Delete extends DataProvider\AnsiSQL\Expression\Delete {

    /**
     * @inheritDoc
     */
    public function Execute($Buffered = true): IResult {
        return DataProvider::Execute($this->Statement, $Buffered);
    }

}