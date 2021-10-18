<?php
declare(strict_types=1);

namespace vDesk\Updates;

/**
 * Archive Update manifest class.
 *
 * @package vDesk\Archive
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Archive extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Archive::class;

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

    public static function Install(\Phar $Phar, string $Path): void {
        self::Undeploy();
        self::Deploy($Phar, $Path);
    }
}