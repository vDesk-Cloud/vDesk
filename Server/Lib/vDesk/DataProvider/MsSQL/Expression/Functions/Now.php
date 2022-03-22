<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression\Functions;

/**
 * SQL function 'NOW()'.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Now extends \vDesk\DataProvider\AnsiSQL\Expression\Functions\Now {

    /**
     * @inheritDoc
     */
    public function __toString(): string {
        return "GETDATE()";
    }
    
}