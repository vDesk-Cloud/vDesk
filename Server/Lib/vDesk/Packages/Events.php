<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Archive\Element;
use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Collation;
use vDesk\DataProvider\Type;
use vDesk\IO\Directory;
use vDesk\IO\Path;
use vDesk\Locale\IPackage;
use vDesk\Modules\Module\Command;
use vDesk\Security\AccessControlList;
use vDesk\Security\User;
use vDesk\Struct\Guid;
use vDesk\Struct\Text;
use vDesk\Utils\Log;

/**
 * Events Package manifest class.
 *
 * @package vDesk\Events
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Events extends Package implements IPackage {

    /**
     * The name of the Package.
     */
    public const Name = "Events";

    /**
     * The version of the Package.
     */
    public const Version = "1.2.0";

    /**
     * The vendor of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";

    /**
     * The description of the Package.
     */
    public const Description = "Package providing functionality for dispatching and listening on global events.";

    /**
     * The dependencies of the Package.
     */
    public const Dependencies = [
        "Modules"       => "1.0.2",
        "Configuration" => "1.1.0"
    ];

    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Lib => [
                "vDesk/Events.js",
                "vDesk/Events"
            ]
        ],
        self::Server => [
            self::Modules => [
                "EventDispatcher.php"
            ],
            self::Lib     => [
                "vDesk/Events"
            ]
        ]
    ];

    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "Settings" => [
                "Events:Interval" => "Legt den Intervall in Sekunden fest, in dem neue Events versendet werden."
            ]
        ],
        "EN" => [
            "Settings" => [
                "Events:Interval" => "Defines the interval in seconds at which new events are dispatched."
            ]
        ],
        "NL" => [
            "Settings" => [
                "Events:Interval" => "Bepaalt het interval in seconden waarmee nieuwe gebeurtenissen worden verzonden."
            ]
        ]
    ];

    /**
     * The available storage modes of Event listeners.
     */
    public const Modes = ["Filesystem", "Archive", "Both"];

    /**
     * @var string
     */
    private static string $Mode = "Filesystem";

    /** @inheritDoc */
    public static function PreInstall(\Phar $Phar, string $Path): void {

        //Suggest.
        if((int)\ini_get("max_execution_time") > 0) {
            Log::Warn(self::Name, "Package suggests setting ini value of \"max_execution_time\" to 0.");
        }

        //Check if the Archive package has been bundled.
        if(\PHP_SAPI === "cli" && \vDesk\Modules::Installed("Archive")) {
            $Parameters = \getopt("", ["Events.Mode", "ev.md"]);
            $Mode       = $Parameters["Events.Mode"] ?? $Parameters["ev.md"];
            while(!\in_array($Mode, self::Modes)){
                $Mode = \readline("Event listener storage mode [available = " . \implode(", ", self::Modes) . "] [default = Filesystem]: ");
                $Mode = Text::IsNullOrWhitespace($Mode) ? "Filesystem" : $Mode;
            }
        } else {
            self::$Mode = \vDesk\Modules::Events()::Filesystem;
        }

        //Create local config.
        Settings::$Local["Events"] = new Settings\Local\Settings(["Mode" => self::$Mode, "Directory" => null], "Events");
    }

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {

        //Create schema.
        Expression::Create()
                  ->Schema("Events")
                  ->Execute();

        //Create tables.
        Expression::Create()
                  ->Table(
                      "Events.Public",
                      [
                          "TimeStamp" => ["Type" => Type::BigInt | Type::Unsigned],
                          "Name"      => ["Type" => Type::VarChar, "Size" => 255, "Collation" => Collation::UTF8],
                          "Data"      => ["Type" => Type::VarChar, "Size" => 2000, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null]
                      ],
                      [
                          "TimeStamp" => ["Fields" => ["TimeStamp"]]
                      ],
                      [
                          "Engine" => "MEMORY"
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "Events.Private",
                      [
                          "TimeStamp" => ["Type" => Type::BigInt | Type::Unsigned],
                          "Receiver"  => ["Type" => Type::BigInt | Type::Unsigned],
                          "Name"      => ["Type" => Type::VarChar, "Size" => 255, "Collation" => Collation::UTF8],
                          "Data"      => ["Type" => Type::VarChar, "Size" => 2000, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null]
                      ],
                      [
                          "Receiver" => ["Fields" => ["TimeStamp", "Receiver"]]
                      ],
                      [
                          "Engine" => "MEMORY"
                      ]
                  )
                  ->Execute();

        //Install Module.
        /** @var \Modules\Events $Events */
        $Events = \vDesk\Modules::Events();
        $Events->Commands->Add(new Command(null, $Events, "Stream", true, false));
        $Events->Save();

        //Create remote config.
        Settings::$Remote["Events"] = new Settings\Remote\Settings(
            [
                "Interval" => new Settings\Remote\Setting(
                    "Interval",
                    10,
                    \vDesk\Struct\Type::Int,
                    false,
                    true
                )
            ],
            "Events"
        );

        //Create Event listener storage.
        Directory::Create($Path . Path::Separator . self::Server . Path::Separator . self::Name);

        //Extract files.
        self::Deploy($Phar, $Path);
    }

    /** @inheritDoc */
    public static function PostInstall(\Phar $Phar, string $Path): void {

        //Check if Archive storage option is set.
        if(self::$Mode & \vDesk\Modules::Events()::Archive) {
            $System = new Element(2);
            $Events = new Element(
                null,
                User::$Current,
                $System,
                "Events",
                Element::Folder,
                new \DateTime("now"),
                Guid::Create(),
                null,
                null,
                0,
                null,
                new AccessControlList($System->AccessControlList)
            );
            $Events->Save();
            Settings::$Local["Events"]["Directory"] = $Events->ID;
        }

        Settings::$Local["Events"]->Save();
    }

    /** @inheritDoc */
    public static function Uninstall(string $Path): void {

        //Delete Archive storage element.
        if(Settings::$Local["Events"]["Directory"] & \vDesk\Modules::Events()::Archive && \vDesk\Modules::Installed("Archive")) {
            \vDesk\Modules::Archive()::DeleteElements([Settings::$Local["Events"]["Directory"]]);
        }

        //Uninstall Module.
        \vDesk\Modules::Events()->Delete();

        //Drop database.
        Expression::Drop()
                  ->Schema("Events")
                  ->Execute();

        //Delete Event listener storage.
        Directory::Delete($Path . Path::Separator . self::Server . Path::Separator . self::Name, true);

        //Delete files.
        self::Undeploy();
    }

}