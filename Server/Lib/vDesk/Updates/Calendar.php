<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * Calendar Update manifest class.
 *
 * @package vDesk\Calendar
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Calendar extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Calendar::class;

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
            Package::Server => [
                Package::Lib => [
                    "vDesk/Calendar/Event/Created.php",
                    "vDesk/Calendar/Event/Updated.php",
                    "vDesk/Calendar/Event/Deleted.php",
                    "vDesk/Calendar/vDesk.Security.User.Deleted.Calendar.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                Package::Lib => [
                    "vDesk/Calendar/Event/Created.php",
                    "vDesk/Calendar/Event/Updated.php",
                    "vDesk/Calendar/Event/Deleted.php",
                    "vDesk/Calendar/vDesk.Security.User.Deleted.php"
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
        \vDesk\Modules::Events()::Install(new self::Package);
    }
}