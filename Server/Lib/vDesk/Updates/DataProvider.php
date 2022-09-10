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
    public const RequiredVersion = "1.0.1";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Fixed field escapement of CREATE and DROP statements.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Server => [
                Package::Lib => [
                    "vDesk/DataProvider/AnsiSQL/Expression/Create.php",
                    "vDesk/DataProvider/AnsiSQL/Expression/Drop.php",
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                Package::Lib => [
                    "vDesk/DataProvider/AnsiSQL/Expression/Create.php",
                    "vDesk/DataProvider/AnsiSQL/Expression/Drop.php",
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