<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider\MySQL\Expression\Functions\Group;

/**
 * Facade that provides factory methods to create MySQL compatible aggregate functions.
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
     * @return \vDesk\DataProvider\MySQL\Expression\Functions\Group
     */
    public static function Group(...$Values): Group {
        return new Group(...$Values);
    }

}