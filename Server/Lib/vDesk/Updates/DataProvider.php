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
    public const RequiredVersion = "1.1.0";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Fixed creating and dropping indices from tables.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Server => [
                Package::Lib => [
                    "vDesk/DataProvider/MsQL/Expression/Create.php",
                    "vDesk/DataProvider/MsQL/Expression/Drop.php",
                    "vDesk/DataProvider/MsQL/Expression/Functions.php",
                    "vDesk/DataProvider/MyQL/Expression/Create.php",
                    "vDesk/DataProvider/MyQL/Expression/Drop.php",
                    "vDesk/DataProvider/PgQL/Expression/Create.php",
                    "vDesk/DataProvider/PgQL/Expression/Drop.php"
                ]
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {
        //Update files.
        self::Deploy($Phar, $Path);
    }
}