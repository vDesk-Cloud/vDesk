<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

/**
 * Represents a PgSQL compatible DELETE SQL expression.
 *
 * @package vDesk\DataProvider\PgSQL
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Delete extends \vDesk\DataProvider\AnsiSQL\Expression\Delete {
    //Postgres' DROP is ANSI conform.
}