<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression\Functions;

/**
 * SQL function 'CURRENT_TIMESTAMP()'.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class CurrentTimestamp extends \vDesk\DataProvider\AnsiSQL\Expression\Functions\CurrentTimestamp {

    /**
     * @inheritDoc
     */
    public function __toString(): string {
        return "CURRENT_TIMESTAMP";
    }
}