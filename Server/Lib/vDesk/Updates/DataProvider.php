<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * DataProvider Update manifest class.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class DataProvider extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\DataProvider::class;

    /**
     * The required Package version of the Update.
     */
    public const RequiredVersion = "1.0.2";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Added possibility for inserting rows into tables consisting only of a single identity column.
- Added missing reserved keywords.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Server => [
                Package::Lib => [
                    "vDesk/DataProvider/MsQL/Provider.php",
                    "vDesk/DataProvider/MsQL/Expression/Insert.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                Package::Lib => [
                    "vDesk/DataProvider/MsQL/Provider.php",
                    "vDesk/DataProvider/MsQL/Expression/Insert.php"
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