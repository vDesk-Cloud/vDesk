<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * Configuration Update manifest class.
 *
 * @package vDesk\Configuration
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Configuration extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Configuration::class;

    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.0.1";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Added compatibility to vDesk-1.1.0.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Client => [
                Package::Lib => [
                    "vDesk/Configuration/Settings.js",
                    "vDesk/Configuration/Remote/Plugins/Log.js",
                    "vDesk/Configuration/Remote/Plugins/Settings.js"
                ]
            ],
            Package::Server => [
                Package::Modules => [
                    "Configuration.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Lib    => [
                "vDesk/Configuration/Settings.js",
                "vDesk/Configuration/Remote/Plugins/Log.js",
                "vDesk/Configuration/Remote/Plugins/Settings.js"
            ],
            Package::Server => [
                Package::Modules => [
                    "Configuration.php"
                ]
            ]
        ]
    ];

    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        //Update files.
        self::Undeploy();
        self::Deploy($Phar, $Path);
    }
}