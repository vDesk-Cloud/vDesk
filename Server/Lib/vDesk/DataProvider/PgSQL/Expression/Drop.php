<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

/**
 * Represents a PgSQL compatible DROP SQL expression.
 *
 * @package vDesk\DataProvider\PgSQL
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Drop extends \vDesk\DataProvider\AnsiSQL\Expression\Drop {
    //Postgres' DROP is ANSI conform.
}