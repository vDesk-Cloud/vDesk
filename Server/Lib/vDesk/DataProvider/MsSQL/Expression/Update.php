<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression;

/**
 * Represents a MsSQL compatible "UPDATE" Expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Update extends \vDesk\DataProvider\AnsiSQL\Expression\Update {
    //MsSQL's UPDATE is ANSI conform.
}