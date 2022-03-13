<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * Modules Update manifest class.
 *
 * @package vDesk\Modules
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Modules extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Modules::class;

    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.0.0";

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
                    "vDesk/Modules"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Client => [
                Package::Lib => [
                    "vDesk/Modules"
                ]
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {
        //Update files.
        self::Undeploy();
        self::Deploy($Phar, $Path);
    }
}