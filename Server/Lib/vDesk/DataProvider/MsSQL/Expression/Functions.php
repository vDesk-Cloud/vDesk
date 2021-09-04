<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression;

use vDesk\DataProvider\MsSQL\Expression\Functions\Group;

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
}