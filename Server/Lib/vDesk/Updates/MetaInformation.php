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
            Package::Server => [
                Package::Lib => [
                    "/vDesk/MetaInformation/vDesk.Archive.Element.Deleted.MetaInformation.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                Package::Lib => [
                    "/vDesk/MetaInformation/vDesk.Archive.Element.Deleted.php"
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