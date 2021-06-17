<?php
declare(strict_types=1);

namespace vDesk\Crash\Test\Case;

/**
 * Attribute that represents a repeatable Test or case.
 *
 * @package vDesk\Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Repeat extends \vDesk\Crash\Test\Repeat {}