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
    public const RequiredVersion = "1.0.0";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Fixed wrong serialization of boolean values.
Description;

    /**
     * The files of the Update.
     */
    public const Files = [
        Update::Deploy   => [
            Package::Server => [
                Package::Lib => [
                    "vDesk/Configuration/Settings/Local/Settings.php"
                ]
            ]
        ],
        Update::Undeploy => [
            Package::Server => [
                Package::Lib => [
                    "vDesk/Configuration/Settings/Local/Settings.php"
                ]
            ]
        ]
    ];

    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        self::Undeploy();
        self::Deploy($Phar, $Path);
    }
}