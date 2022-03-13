<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Configuration\Settings;
use vDesk\DataProvider\Collation;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Type;
use vDesk\IO\Directory;
use vDesk\IO\Path;
use vDesk\Locale\IPackage;
use vDesk\Modules\Module\Command;
use vDesk\Modules\Module\Command\Parameter;
use vDesk\Struct\Collections\Observable\Collection;
use vDesk\Struct\Extension;

/**
 * UpdateServer Package manifest.
 *
 * @package vDesk\Packages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class UpdateHost extends Package implements IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "UpdateHost";
    
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
    public const Description = "Package providing functionality for hosting updates.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Updates" => "1.0.0"];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Lib => [
                "vDesk/UpdateHost.js"
            ]
        ],
        self::Server => [
            self::Modules => [
                "UpdateHost.php"
            ]
        ]
    ];
    
    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "UpdateHost" => [
                "Hosted" => "Bereitgestellte Updates",
                "Host"   => "Hosten"
            ]
        ],
        "EN" => [
            "UpdateHost" => [
                "Hosted" => "Hosted Updates",
                "Host"   => "Host"
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        Expression::Create()
                  ->Schema("Updates")
                  ->Execute();
        
        //Create table.
        Expression::Create()
                  ->Table(
                      "Updates.Hosted",
                      [
                          "Package"      => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Version"      => ["Type" => Type::TinyText, "Collation" => Collation::ASCII],
                          "Major"        => ["Type" => Type::Int | Type::Unsigned],
                          "Minor"        => ["Type" => Type::Int | Type::Unsigned],
                          "Patch"        => ["Type" => Type::Int | Type::Unsigned],
                          "Hash"         => ["Type" => Type::TinyText, "Collation" => Collation::ASCII],
                          "File"         => ["Type" => Type::TinyText, "Collation" => Collation::ASCII],
                          "Dependencies" => ["Type" => Type::TinyText/** Dependency hell protection */, "Collation" => Collation::UTF8],
                          "Vendor"       => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Description"  => ["Type" => Type::Text, "Collation" => Collation::UTF8]
            
                      ],
                      [
                          "Primary" => ["Fields" => ["Package" => 255, "Major", "Minor", "Patch", "Hash" => 255]]
                      ]
                  )
                  ->Execute();
        
        //Install Module.
        /** @var \Modules\UpdateHost $UpdateHost */
        $UpdateHost = \vDesk\Modules::UpdateHost();
        $UpdateHost->Commands->Add(
            new Command(
                null,
                $UpdateHost,
                "Available",
                false,
                false,
                null,
                new Collection([new Parameter(null, null, "Packages", \vDesk\Struct\Type::Array, false, false)])
            )
        );
        $UpdateHost->Commands->Add(
            new Command(
                null,
                $UpdateHost,
                "Hosted",
                true,
                false
            )
        );
        $UpdateHost->Commands->Add(
            new Command(
                null,
                $UpdateHost,
                "Host",
                true,
                true,
                null,
                new Collection([new Parameter(null, null, "Update", Extension\Type::File, false, false)])
            )
        );
        $UpdateHost->Commands->Add(
            new Command(
                null,
                $UpdateHost,
                "Download",
                false,
                false,
                null,
                new Collection([new Parameter(null, null, "Hash", \vDesk\Struct\Type::String, false, false)])
            )
        );
        $UpdateHost->Commands->Add(
            new Command(
                null,
                $UpdateHost,
                "Remove",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "Hash", \vDesk\Struct\Type::String, false, false)])
            )
        );
        $UpdateHost->Save();
        
        //Create Update directory.
        Settings::$Local["UpdateHost"] = new Settings\Local\Settings(
            ["Directory" => Directory::Create($Path . Path::Separator . self::Server . Path::Separator . "Updates")->Path],
            "UpdateHost"
        );
        
        //Extract files.
        self::Deploy($Phar, $Path);
    }
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        
        //Uninstall Module.
        /** @var \Modules\Packages $Updates */
        $Updates = \vDesk\Modules::Updates();
        $Updates->Delete();

        //Drop schema.
        Expression::Drop()
                  ->Schema("Updates")
                  ->Execute();
        
        //Delete files.
        Directory::Delete($Path . Path::Separator . self::Server . Path::Separator . "Updates", true);
        self::Undeploy();
        
    }
}