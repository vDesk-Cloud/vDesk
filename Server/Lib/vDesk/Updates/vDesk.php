<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Configuration\Settings;
use vDesk\Packages\Package;

/**
 * vDesk Update manifest class.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class vDesk extends Update {
    /**
     * The class name of the Package of the Update.
     */
    public const Package = \vDesk\Packages\vDesk::class;

    /**
     * The required Package version of the Update.
     */
    public const RequiredVersion = "1.0.0";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Fixed missing context for error logging.
- Fixed Socket::Select().
- Fixed Collections.
- Removed unused files.
- Implemented auto initialization of DataProvider- and Expression-facades.
- Moved DataProvider to separate package.
- Removed IStream::Open()-method.
- Removed unused files.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Server => [
                Package::Lib => [
                    "vDesk.php",
                    "vDesk/Data",
                    "vDesk/Environment",
                    "vDesk/IO",
                    "vDesk/Struct",
                    //Install new DataProvider.
                    "vDesk/DataProvider",
                    "vDesk/DataProvider.php",
                    "vDesk/Packages/DataProvider.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                Package::Lib => [
                    "vDesk.php",
                    "vDesk/Data",
                    "vDesk/Environment",
                    "vDesk/IO",
                    "vDesk/Struct",
                    //Uninstall old DataProvider.
                    "vDesk/DataProvider",
                    "vDesk/DataProvider.php"
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

        //Update config.
        Settings::$Local["DataProvider"]["Persistent"] = false;
        Settings::$Local["DataProvider"]["Database"] = "vDesk";
        Settings::$Local["DataProvider"]["Server"] = ltrim(Settings::$Local["DataProvider"]["Server"], "p:");
        Settings::$Local->Save();
    }
}