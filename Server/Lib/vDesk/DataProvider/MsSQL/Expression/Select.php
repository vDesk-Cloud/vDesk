<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression;

use vDesk\DataProvider;

/**
 * Represents a MsSQL compatible "SELECT" Expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Select extends DataProvider\AnsiSQL\Expression\Select {
    //Postgres' SELECT is ANSI conform.
}