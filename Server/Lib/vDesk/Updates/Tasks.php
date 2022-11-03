<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * Tasks Update manifest class.
 *
 * @package vDesk\Machines
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Tasks extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Tasks::class;

    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.0.0";

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
                    "vDesk/Tasks/Tasks.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                Package::Lib => [
                    "vDesk/Tasks/Tasks.php"
                ]
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {
        //Remove old Task dispatcher.
        \vDesk\Modules::Machines()::Uninstall(new self::Package, $Path);

        //Update files.
        self::Undeploy();
        self::Deploy($Phar, $Path);

        //Reinstall Package.
        \vDesk\Modules::Machines()::Install(new self::Package, $Path);
    }
}