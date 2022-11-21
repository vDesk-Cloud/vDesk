<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

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
    public const RequiredVersion = "1.1.0";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Added compatibility to Events-1.2.0.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Client => [
                Package::Modules => [
                    "Archive.js"
                ]
            ],
            Package::Server => [
                Package::Lib => [
                    "vDesk/Archive/Element",
                    "vDesk/Archive/vDesk.Security.User.Deleted.Archive.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Client => [
                Package::Modules => [
                    "Archive.js"
                ]
            ],
            Package::Server => [
                Package::Lib => [
                    "vDesk/Archive/Element",
                    "vDesk/Archive/vDesk.Security.User.Deleted.php"
                ]
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {
        //Update files.
        self::Undeploy();
        self::Deploy($Phar, $Path);

        //Install new Event listener.
        \vDesk\Modules::Events()::Install(new (self::Package), $Phar, $Path);
    }
}