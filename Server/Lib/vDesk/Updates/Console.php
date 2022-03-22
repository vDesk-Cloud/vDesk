<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * Console Update manifest class.
 *
 * @package vDesk\Contacts
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Console extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Console::class;

    /**
     * The required Package version of the Update.
     */
    public const RequiredVersion = "1.0.0";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Added compatibility to vDesk-1.1.0.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Client => [
                Package::Lib     => [
                    "vDesk/Console/Commands/Logout.js"
                ],
                Package::Modules => [
                    "Console.js"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Client => [
                Package::Lib     => [
                    "vDesk/Console/Commands/Logout.js"
                ],
                Package::Modules => [
                    "Console.js"
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