<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

/**
 * Represents a PgSQL compatible "UPDATE" Expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Update extends \vDesk\DataProvider\AnsiSQL\Expression\Update {
    //Postgres' UPDATE is ANSI conform.
}