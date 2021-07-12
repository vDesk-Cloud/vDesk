<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider\IResult;
use vDesk\DataProvider;

/**
 * Represents a MySQL compatible DROP SQL expression.
 *
 * @package vDesk\DataProvider\Expression\Drop
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Drop extends DataProvider\AnsiSQL\Expression\Drop {

    /**
     * @inheritDoc
     */
    public function Execute(bool $Buffered = true): IResult {
        return DataProvider::Execute($this->Statement, $Buffered);
    }

}