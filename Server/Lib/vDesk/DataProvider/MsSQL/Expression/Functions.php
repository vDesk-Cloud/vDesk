<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression;

use vDesk\DataProvider\MsSQL\Expression\Functions\Now;
use vDesk\DataProvider\MsSQL\Expression\Functions\Group;
use vDesk\DataProvider\MsSQL\Expression\Functions\CurrentTimestamp;

/**
 * Facade that provides factory methods to create MsSQL compatible aggregate functions.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Functions extends \vDesk\DataProvider\AnsiSQL\Expression\Functions {
    
    /**
     * GROUP_CONCAT().
     *
     * @param array $Values
     *
     * @return \vDesk\DataProvider\MsSQL\Expression\Functions\Group
     */
    public static function Group(...$Values): Group {
        return new Group(...$Values);
    }

    /** @inheritDoc */
    public static function CurrentTimestamp(): CurrentTimestamp {
        return new CurrentTimestamp();
    }

    /** @inheritDoc */
    public static function Now(): Now {
         return new Now();
    }
}