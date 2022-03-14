<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

/**
 * Represents a MySQL compatible "UPDATE" Expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Update extends \vDesk\DataProvider\AnsiSQL\Expression\Update {
    //MySQL's UPDATE is ANSI conform.
}