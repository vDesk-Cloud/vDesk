<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

/**
 * Represents a PgSQL compatible INSERT SQL expression.
 *
 * @package vDesk\DataProvider\PgSQL
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Insert extends \vDesk\DataProvider\AnsiSQL\Expression\Insert {
    //Postgres' INSERT is ANSI conform.
}