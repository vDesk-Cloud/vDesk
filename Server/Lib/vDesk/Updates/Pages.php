<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Configuration\Settings;
use vDesk\IO\Directory;
use vDesk\IO\Path;
use vDesk\Packages\Package;

/**
 * Pages Update manifest class.
 *
 * @package vDesk\Pages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Pages extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Pages::class;

    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.0.1";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Implemented caching functionality.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Server => [
                Package::Lib     => [
                    "vDesk/Pages/Cached/Page"
                ]
            ]
        ]
    ];

    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        Settings::$Local["Pages"]["Cache"] = (Directory::Create($Path . Path::Separator . Package::Server . Path::Separator . "Cache"))->Path;
        Settings::$Local->Save();
        self::Deploy($Phar, $Path);
    }
}