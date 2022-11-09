<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * Machines Update manifest class.
 *
 * @package vDesk\Machines
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Machines extends Update {
    
    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Machines::class;
    
    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.0.2";

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
            Package::Client => [
                Package::Lib => [
                    "vDesk/Machines/Machine.js",
                    "vDesk/Machines/Administration.js"
                ]
            ],
            Package::Server => [
                Package::Modules => [
                    "Machines.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Client => [
                Package::Lib => [
                    "vDesk/Machines/Machine.js",
                    "vDesk/Machines/Administration.js"
                ]
            ],
            Package::Server => [
                Package::Modules => [
                    "Machines.php"
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