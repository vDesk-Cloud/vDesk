<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

use vDesk\DataProvider\PgSQL\Expression\Functions\GroupConcat;

/**
 * Factory-facade that provides access to PgSQL compatible aggregate functions.
 *
 * @package vDesk\DataProvider\PgSQL
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Functions  extends \vDesk\DataProvider\AnsiSQL\Expression\Functions {
    

    /**
     * GROUP_CONCAT().
     *
     * @param array $Values
     *
     * @return \vDesk\DataProvider\PgSQL\Expression\Functions\GroupConcat
     */
    public static function Group(...$Values): GroupConcat {
        return new GroupConcat(...$Values);
    }

}