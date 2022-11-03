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
    public const RequiredVersion = "1.1.0";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Reworked collections.
- Reworked streams.
- Removed global reference to current User.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Server => [
                Package::Lib => [
                    "vDesk.php",
                    "vDesk/IO",
                    "vDesk/Struct/Collections",
                    "vDesk/Client.php",
                    "vDesk/Utils/Log.php",
                    "vDesk/Utils/Validate.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                Package::Lib => [
                    "vDesk.php",
                    "vDesk/IO",
                    "vDesk/Struct/Collections",
                    "vDesk/Client.php",
                    "vDesk/Utils/Log.php",
                    "vDesk/Utils/Validate.php"
                ]
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {
        //Update files.
        self::Undeploy();
        self::Deploy($Phar, $Path);
    }
}