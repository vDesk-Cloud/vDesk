<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Collation;
use vDesk\DataProvider\Type;
use vDesk\Modules\Module\Command;

use vDesk\Struct\Collections\Observable\Collection;

/**
 * Class Packages represents ...
 *
 * @package vDesk\Packages\Packages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Modules extends Package {
    
    /**
     * The name of the Package.
     */
    public const Name = self::Modules;
    
    /**
     * The version of the Package.
     */
    public const Version = "1.0.1";
    
    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";
    
    /**
     * The name of the Package.
     */
    public const Description = "Package providing functionality for running Modules and inter process communication between Modules.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["DataProvider" => "1.0.0"];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design => [
                "vDesk/Modules"
            ],
            self::Lib    => [
                "vDesk/Modules.js",
                "vDesk/Modules"
            ]
        ],
        self::Server => [
            self::Modules => [
                "Modules.php"
            ],
            self::Lib     => [
                "vDesk/Modules.php",
                "vDesk/Modules"
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        Expression::Create()
                  ->Schema(self::Modules)
                  ->Execute();
        
        //Create tables.
        Expression::Create()
                  ->Table(
                      "Modules.Modules",
                      [
                          "ID"     => ["Type" => Type::SmallInt | Type::Unsigned, "Autoincrement" => true],
                          "Name"   => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Remote" => ["Type" => Type::Boolean, "Default" => false]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID", "Name" => 255]]
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "Modules.Commands",
                      [
                          "ID"            => ["Type" => Type::Int | Type::Unsigned, "Autoincrement" => true],
                          "Module"        => ["Type" => Type::SmallInt | Type::Unsigned],
                          "Name"          => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "RequireTicket" => ["Type" => Type::Boolean],
                          "Binary"        => ["Type" => Type::Boolean],
                          "Alias"         => ["Type" => Type::TinyText, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID", "Name" => 255]]
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "Modules.Parameters",
                      [
                          "ID"       => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "Command"  => ["Type" => Type::Int | Type::Unsigned],
                          "Name"     => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Type"     => ["Type" => Type::TinyText, "Collation" => Collation::ASCII],
                          "Optional" => ["Type" => Type::Boolean],
                          "Nullable" => ["Type" => Type::Boolean]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID", "Command"]]
                      ]
                  )
                  ->Execute();
        
        //Install Module.
        /** @var \Modules\Modules $Modules */
        $Modules = \vDesk\Modules::Modules();
        $Modules->Commands->Add(
            new Command(
                null,
                $Modules,
                "Connect",
                true,
                false,
                null,
                new Collection([
                    new Command\Parameter(
                        null,
                        null,
                        "Version",
                        \vDesk\Struct\Type::String, //@todo Change to Object.
                        false,
                        false
                    )
                ])
            )
        );
        $Modules->Commands->Add(
            new Command(
                null,
                $Modules,
                "GetCommands",
                true,
                false
            )
        );
        $Modules->Commands->Add(
            new Command(
                null,
                $Modules,
                "Status",
                true,
                false
            )
        );
        $Modules->Save();
        
        //Extract files.
        self::Deploy($Phar, $Path);
    }
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        
        //Uninstall Module.
        /** @var \Modules\Modules $Modules */
        $Modules = \vDesk\Modules::Modules();
        $Modules->Delete();
        
        //Drop database.
        Expression::Drop()
                  ->Schema(self::Modules)
                  ->Execute();
        
        //Delete files.
        self::Undeploy();
        
    }
}