<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Archive\Element;
use vDesk\Configuration\Settings;
use vDesk\DataProvider\Collation;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Type;
use vDesk\Locale\IPackage;
use vDesk\Modules\Module\Command;
use vDesk\Modules\Module\Command\Parameter;
use vDesk\Security\AccessControlList;
use vDesk\Security\User;
use vDesk\Struct\Collections\Observable\Collection;
use vDesk\Struct\Guid;

/**
 * Machines Package manifest class.
 *
 * @package vDesk\Machines
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Machines extends Package implements IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "Machines";
    
    /**
     * The version of the Package.
     */
    public const Version = "1.0.3";
    
    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";
    
    /**
     * The name of the Package.
     */
    public const Description = "Package providing an OS agnostic process manager for PHP.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Archive" => "1.0.2"];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design  => [
                "vDesk/Machines.css",
                "vDesk/Machines"
            ],
            self::Modules => [
                "Machines.js"
            ],
            self::Lib     => [
                "vDesk/Machines.js",
                "vDesk/Machines"
            ]
        ],
        self::Server => [
            self::Modules => [
                "Machines.php"
            ],
            self::Lib     => [
                "vDesk/Machines"
            ]
        ]
    ];
    
    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "Machines" => [
                "Machines"  => "Machines",
                "Running"   => "Running",
                "Suspended" => "Suspended",
                "Start"     => "Starten",
                "Suspend"   => "Anhalten",
                "Resume"    => "Fortsetzen",
                "Stop"      => "Beenden",
                "Terminate" => "Terminieren",
                "Reap"      => "Zombie Maschinen entfernen",
                "Status"    => "Status",
                "TimeStamp" => "Startzeit"
            ],
            "Permissions" => [
                "RunMachine" => "Legt fest ob Mitglieder der Gruppe Maschinen ausführen können"
            ]
        ],
        "EN" => [
            "Machines" => [
                "Machines"  => "Machines",
                "Running"   => "Running",
                "Suspended" => "Suspended",
                "Start"     => "Start",
                "Suspend"   => "Suspend",
                "Resume"    => "Resume",
                "Stop"      => "Stop",
                "Terminate" => "Terminate",
                "Reap"      => "Remove zombie Machines",
                "Status"    => "Status",
                "TimeStamp" => "Start time"
            ],
            "Permissions" => [
                "RunMachine" => "Determines whether members of the group are allowed to run machines"
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        Expression::Create()
                  ->Schema("Machines")
                  ->Execute();
        
        //Create tables.
        Expression::Create()
                  ->Table(
                      "Machines.Running",
                      [
                          "ID"        => ["Type" => Type::SmallInt | Type::Unsigned, "Nullable" => true],
                          "Owner"     => ["Type" => Type::BigInt | Type::Unsigned],
                          "Guid"      => ["Type" => Type::VarChar, "Size" => 255, "Collation" => Collation::UTF8],
                          "TimeStamp" => ["Type" => Type::BigInt | Type::Unsigned, "Nullable" => true],
                          "Status"    => ["Type" => Type::Char, "Size" => 1, "Collation" => Collation::ASCII],
                          "Name"      => ["Type" => Type::VarChar, "Size" => 255, "Collation" => Collation::UTF8]
                      ],
                      ["PID" => ["Fields" => ["ID", "Guid"]]],
                      ["Engine" => "MEMORY"]
                  )
                  ->Execute();
        
        //Install Module.
        /** @var \Modules\Machines $Machines */
        $Machines = \vDesk\Modules::Machines();
        $Machines->Commands->Add(
            new Command(
                null,
                $Machines,
                "Installed",
                true,
                false
            )
        );
        $Machines->Commands->Add(
            new Command(
                null,
                $Machines,
                "Running",
                true,
                false
            )
        );
        $Machines->Commands->Add(
            new Command(
                null,
                $Machines,
                "Start",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "Name", \vDesk\Struct\Type::String, false, false)])
            )
        );
        $Machines->Commands->Add(
            new Command(
                null,
                $Machines,
                "Run",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "Guid", \vDesk\Struct\Type::String, false, false)])
            )
        );
        $Machines->Commands->Add(
            new Command(
                null,
                $Machines,
                "Suspend",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "Guid", \vDesk\Struct\Type::String, false, false)])
            )
        );
        $Machines->Commands->Add(
            new Command(
                null,
                $Machines,
                "Resume",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "Guid", \vDesk\Struct\Type::String, false, false)])
            )
        );
        $Machines->Commands->Add(
            new Command(
                null,
                $Machines,
                "Stop",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "Guid", \vDesk\Struct\Type::String, false, false)])
            )
        );
        $Machines->Commands->Add(
            new Command(
                null,
                $Machines,
                "Terminate",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "Guid", \vDesk\Struct\Type::String, false, false)])
            )
        );
        $Machines->Commands->Add(
            new Command(
                null,
                $Machines,
                "Reap",
                true,
                false
            )
        );
        $Machines->Save();
    
        //Create Machines folder.
        $System   = new Element(2);
        $Directory = new Element(
            null,
            User::$Current,
            $System,
            "Machines",
            Element::Folder,
            new \DateTime("now"),
            Guid::Create(),
            null,
            null,
            0,
            null,
            new AccessControlList($System->AccessControlList)
        );
        $Directory->Save();
        
        Settings::$Local["Machines"]  = new Settings\Local\Settings(["Directory" => $Directory->ID], "Machines");
        Settings::$Remote["Machines"] = new Settings\Remote\Settings(
            ["Limit" => new Settings\Remote\Setting("Limit", 24, \vDesk\Struct\Type::Int)],
            "Machines"
        );
        
        //Create permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::CreatePermission("RunMachine", false);
        
        //Extract files.
        self::Deploy($Phar, $Path);
    }
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        
        //Uninstall Module.
        /** @var \Modules\Machines $Machines */
        $Machines = \vDesk\Modules::Machines();
        $Machines->Delete();
        
        //Drop database.
        Expression::Drop()
                  ->Schema("Machines")
                  ->Execute();
        
        //Delete permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::DeletePermission("RunMachine");
        
        //Delete files.
        self::Undeploy();
        
    }
}