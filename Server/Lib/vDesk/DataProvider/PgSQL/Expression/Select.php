<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

/**
 * Represents a PgSQL compatible SELECT SQL expression.
 *
 * @package vDesk\DataProvider\PgSQL
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Select extends \vDesk\DataProvider\AnsiSQL\Expression\Select {
    //Postgres' SELECT is ANSI conform.
}