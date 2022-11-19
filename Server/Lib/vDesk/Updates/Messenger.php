<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * Messenger Update manifest class.
 *
 * @package vDesk\Messenger
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Messenger extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Messenger::class;

    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.1.0";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Added compatibility to Events-1.2.0.
- Added event listener for deleting messages of deleted users.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Client => [
                Package::Modules => [
                    "Messenger.js"
                ]
            ],
            Package::Server => [
                Package::Lib => [
                    "vDesk/Messenger/Users/Message",
                    "vDesk/Messenger/Groups/Message",
                    "vDesk/Messenger/vDesk.Security.User.Deleted.Messenger.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Client => [
                Package::Modules => [
                    "Messenger.js"
                ]
            ],
            Package::Server => [
                Package::Lib => [
                    "vDesk/Messenger/Users/Message",
                    "vDesk/Messenger/Groups/Message"
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
        \vDesk\Modules::Events()::Install(new (self::Package));
    }
}