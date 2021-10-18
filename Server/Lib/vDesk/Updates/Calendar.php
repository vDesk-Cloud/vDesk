<?php
declare(strict_types=1);

namespace vDesk\Updates;

/**
 * Calendar Update manifest class.
 *
 * @package vDesk\Calendar
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Calendar extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Calendar::class;

    /**
     * The required Package version of the Update.
     */
    public const RequiredVersion = "1.0.0";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Added compatibility to vDesk-1.0.0.
Description;

    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        //Just update Package manifest.
    }
}