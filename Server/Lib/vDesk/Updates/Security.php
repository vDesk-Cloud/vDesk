<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * Security Update manifest class.
 *
 * @package vDesk\Security
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Security extends Update {

    /**
     * The class name of the Package of the Update.
     */
    public const Package = \vDesk\Packages\Security::class;

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
                Package::Lib     => [
                    "vDesk/Security/Group/Created.php",
                    "vDesk/Security/Group/Updated.php",
                    "vDesk/Security/Group/Deleted.php",
                    "vDesk/Security/User/Created.php",
                    "vDesk/Security/User/Updated.php",
                    "vDesk/Security/User/Deleted.php"
                ],
                Package::Modules => [
                    "Security.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                Package::Lib     => [
                    "vDesk/Security/Group/Added.php",
                    "vDesk/Security/Group/Deleted.php",
                    "vDesk/Security/User/Added.php",
                    "vDesk/Security/User/Updated.php",
                    "vDesk/Security/User/Deleted.php"
                ],
                Package::Modules => [
                    "Security.php"
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