<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\IO\Directory;
use vDesk\IO\Path;
use vDesk\Modules\Module\Command;
use vDesk\Packages\Package;

/**
 * Events Update manifest class.
 *
 * @package vDesk\Events
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Events extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Events::class;

    /**
     * The required Package version of the Update.
     */
    public const RequiredVersion = "1.1.0";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Implemented file system based storage of Event listeners.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Client => [
                Package::Lib => [
                    "vDesk/Events/EventDispatcher.js"
                ]
            ],
            Package::Server => [
                Package::Modules => [
                    "Events.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Client => [
                Package::Lib => [
                    "vDesk/Events/EventDispatcher.js"
                ]
            ],
            Package::Server => [
                Package::Modules => [
                    "EventDispatcher.php"
                ]
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {

        /** @var \Modules\Events $Events */
        $Events = \vDesk\Modules::Events()->Fill();
        $Old    = $Events->Commands->Find(static fn(Command $Command): bool => $Command->Name === "GetElements");
        if($Old !== null){
            $Events->Commands->Remove($Old);
        }
        $Events->Commands->Add(new Command(null, $Events, "Stream", true, false));
        $Events->Save();

        //Create Event listener storage.
        Directory::Create($Path . Path::Separator . Package::Server . Path::Separator . "Events");

        //Update files.
        self::Undeploy();
        self::Deploy($Phar, $Path);
    }
}