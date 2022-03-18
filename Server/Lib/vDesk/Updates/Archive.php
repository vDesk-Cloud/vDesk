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
                Package::Lib     => [
                    "vDesk/Archive"
                ],
                Package::Modules => [
                    "Archive.js"
                ]
            ],
            Package::Server => [
                Package::Modules => [
                    "Archive.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Client => [
                Package::Lib     => [
                    "vDesk/Archive"
                ],
                Package::Modules => [
                    "Archive.js"
                ]
            ],
            Package::Server => [
                Package::Modules => [
                    "Archive.php"
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