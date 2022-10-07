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
    public const RequiredVersion = "1.0.2";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Fixed wrong parameter type of updating ACLs.
- Fixed localisation in user editor.
- Made ACLs compatible to previous changes.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Client => [
                Package::Lib => [
                    "vDesk/Security/User/Editor.js"
                ]
            ],
            Package::Server => [
                Package::Lib     => [
                    "vDesk/Security/AccessControlList.php"
                ],
                Package::Modules => [
                    "Security.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Client => [
                Package::Lib => [
                    "vDesk/Security/User/Editor.js"
                ]
            ],
            Package::Server => [
                Package::Lib     => [
                    "vDesk/Security/AccessControlList.php"
                ],
                Package::Modules => [
                    "Security.php"
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