<?php
declare(strict_types=1);

namespace vDesk\Crash\Test\Case;

/**
 * Attribute that represents a skipped Test case.
 *
 * @package vDesk\Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Skip extends \vDesk\Crash\Test\Skip {}