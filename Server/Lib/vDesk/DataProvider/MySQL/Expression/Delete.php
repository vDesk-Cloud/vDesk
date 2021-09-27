<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider;

/**
 * Represents a MySQL compatible "DELETE" Expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Delete extends DataProvider\AnsiSQL\Expression\Delete {
    //MySQL's DELETE is ANSI conform.
}