<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * MetaInformation Update manifest class.
 *
 * @package vDesk\MetaInformation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class MetaInformation extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\MetaInformation::class;

    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.0.3";

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
                    "vDesk/MetaInformation"
                ]
            ],
            Package::Server => [
                Package::Modules => [
                    "MetaInformation.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Client => [
                Package::Lib => [
                    "vDesk/MetaInformation"
                ]
            ],
            Package::Server => [
                Package::Modules => [
                    "MetaInformation.php"
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