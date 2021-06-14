<?php
declare(strict_types=1);

namespace vDesk\Crash\Test;

use vDesk\Crash\Attribute;

/**
 * Attribute that represents a skipped Test.
 *
 * @package vDesk\Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Skip extends Attribute {}