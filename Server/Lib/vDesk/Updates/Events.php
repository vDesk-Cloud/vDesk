<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Archive\Element;
use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
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
- Removed EventListener class simplifying the syntax of registering listeners.
- Fixed Event data serialization.

Warning: This update will remove every registered event listener from the Archive due to breaking changes!
These will be re-installed in the affected package's following updates.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Client => [
                Package::Lib => [
                    "vDesk/Events/Stream.js"
                ]
            ],
            Package::Server => [
                Package::Lib     => [
                    "vDesk/Events"
                ],
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
                Package::Lib     => [
                    "vDesk/Events"
                ],
                Package::Modules => [
                    "EventDispatcher.php"
                ]
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {

        //Update files.
        self::Undeploy();
        self::Deploy($Phar, $Path);

        //Rename Module.
        Expression::Update("Modules.Modules")
                  ->Set(["Name" => "Events"])
                  ->Where(["Name" => "EventDispatcher"])
                  ->Execute();

        //Replace Command.
        /** @var \Modules\Events $Events */
        $Events = \vDesk\Modules::Events()->Fill();
        $Old    = $Events->Commands->Find(static fn(Command $Command): bool => $Command->Name === "GetElements");
        if($Old !== null) {
            $Events->Commands->Remove($Old);
        }
        $Events->Commands->Add(new Command(null, $Events, "Stream", true, false));
        $Events->Save();

        //Create Event listener storage.
        Directory::Create($Path . Path::Separator . Package::Server . Path::Separator . "Events");

        //Create new config value.
        Settings::$Local["Events"]["Mode"] = $Events::Both;
        Settings::$Local["Events"]->Save();

        //Delete incompatible listeners.
        \vDesk\Modules::Archive()::DeleteElements(
            \vDesk\Modules::Archive()::GetElements(Settings::$Local["Events"]["Directory"])
                          ->Map(fn(Element $Element): int => $Element->ID)
                          ->ToArray()
        );
    }
}