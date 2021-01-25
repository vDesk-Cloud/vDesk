<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Archive\Element;
use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Collation;
use vDesk\DataProvider\Type;
use vDesk\Locale\IPackage;
use vDesk\Modules\Module\Command;

use vDesk\Security\AccessControlList;
use vDesk\Struct\Guid;
use vDesk\Utils\Log;

/**
 * Class Events represents ...
 *
 * @package vDesk\Packages\Packages
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
    public const Version = "1.0.0";
    
    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";
    
    /**
     * The name of the Package.
     */
    public const Description = "Package providing functionality for dispatching and listening on global events.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Modules" => "1.0.0", "Configuration" => "1.0.0"];
    
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
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function PreInstall(\Phar $Phar, string $Path): void {
        if((int)\ini_get("max_execution_time") > 0) {
            Log::Warn(self::Name, "Package suggests setting ini value of \"max_execution_time\" to 0.");
        }
    }
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        Expression::Create()
                  ->Database("Events")
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
        /** @var \Modules\EventDispatcher $EventDispatcher */
        $EventDispatcher = \vDesk\Modules::EventDispatcher();
        $EventDispatcher->Commands->Add(
            new Command(
                null,
                $EventDispatcher,
                "GetEvents",
                false,
                false
            )
        );
        $EventDispatcher->Save();
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
        
        //Extract files.
        self::Deploy($Phar, $Path);
        
    }
    
    /**
     * @inheritDoc
     */
    public static function PostInstall(\Phar $Phar, string $Path): void {
        if(\vDesk\Modules::Installed("Archive")) {
            $System = new Element(2);
            $Events = new Element(
                null,
                \vDesk::$User,
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
            Settings::$Local["Events"]  = new Settings\Local\Settings(["Directory" => $Events->ID], "Events");
            Settings::$Local["Events"]->Save();
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        
        if(\vDesk\Modules::Installed("Archive")) {
            \vDesk\Modules::Archive()::DeleteElements([Settings::$Local["Events"]["Directory"]]);
        }
        
        //Uninstall Module.
        /** @var \Modules\EventDispatcher $EventDispatcher */
        $EventDispatcher = \vDesk\Modules::EventDispatcher();
        $EventDispatcher->Delete();
        
        //Drop database.
        Expression::Drop()
                  ->Database("Events")
                  ->Execute();
        
        //Delete files.
        self::Undeploy();
        
    }
    
}