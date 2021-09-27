<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression;

/**
 * Represents a MsSQL compatible "DELETE" Expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Delete extends \vDesk\DataProvider\AnsiSQL\Expression\Delete {
    //MsSQL's DELETE is ANSI conform.
}