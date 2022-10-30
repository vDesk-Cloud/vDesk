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
    public const RequiredVersion = "1.0.1";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Added compatibility to vDesk-1.2.0.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Server => [
                Package::Lib => [
                    "vDesk/Modules/Module.php",
                    "vDesk/Modules/Module/Command.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                Package::Lib => [
                    "vDesk/Modules/Module.php",
                    "vDesk/Modules/Module/Command.php"
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