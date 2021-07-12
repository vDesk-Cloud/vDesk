<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression;

use vDesk\DataProvider\MsSQL\Expression\Functions\Group;

/**
 * Factory-facade that provides access to MsSQL compatible aggregate functions.
 *
 * @package vDesk\DataProvider\Expression\Functions
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
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