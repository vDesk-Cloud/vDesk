<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider\IResult;
use vDesk\DataProvider;

/**
 * Represents a MySQL compatible UPDATE SQL expression.
 *
 * @package vDesk\DataProvider\Expression\Update
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Update extends DataProvider\AnsiSQL\Expression\Update {
    
    /**
     * @inheritDoc
     */
    public function Execute($Buffered = true): IResult {
        return DataProvider::Execute($this->Statement, $Buffered);
    }

}