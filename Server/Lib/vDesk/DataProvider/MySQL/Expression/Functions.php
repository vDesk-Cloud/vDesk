<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider\MySQL\Expression\Functions\Group;

/**
 * Factory-facade that provides access to MySQL compatible aggregate functions.
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
     * @return \vDesk\DataProvider\MySQL\Expression\Functions\Group
     */
    public static function Group(...$Values): Group {
        return new Group(...$Values);
    }

}