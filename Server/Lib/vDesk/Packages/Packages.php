<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Locale\IPackage;

use vDesk\Modules\Module\Command;
use vDesk\Modules\Module\Command\Parameter;
use vDesk\Struct\Collections\Observable\Collection;
use vDesk\Struct\Type;
use vDesk\Struct\Extension;

/**
 * Packages Package manifest.
 *
 * @package vDesk\Packages\Packages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Packages extends Package implements IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "Packages";
    
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
    public const Description = "Package providing functionality for creating an un/installing packages.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = [
        "Events"   => "1.0.0",
        "Locale"   => "1.0.0",
        "Security" => "1.0.0"
    ];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design => [
                "vDesk/Packages"
            ],
            self::Lib    => [
                "vDesk/Packages.js",
                "vDesk/Packages"
            ]
        ],
        self::Server => [
            self::Modules => [
                "Packages.php"
            ],
            self::Lib     => [
                "vDesk/Packages"
            ]
        ]
    ];
    
    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "Packages"    => [
                "Install"      => "Installieren",
                "Uninstall"    => "Deinstallieren",
                "Packages"     => "Pakete",
                "Package"      => "Paket",
                "Version"      => "Version",
                "Dependencies" => "Abhängigkeiten",
                "Vendor"       => "Herausgeber",
                "Description"  => "Beschreibung"
            ],
            "Permissions" => [
                "InstallPackage"   => "Legt fest ob Mitglieder der Gruppe Pakete installieren können",
                "UninstallPackage" => "Legt fest ob Mitglieder der Gruppe Pakete deinstallieren können"
            ]
        ],
        "EN" => [
            "Packages"    => [
                "Install"      => "Install",
                "Uninstall"    => "Uninstall",
                "Packages"     => "Packages",
                "Package"      => "Package",
                "Version"      => "Version",
                "Dependencies" => "Dependencies",
                "Vendor"       => "Vendor",
                "Description"  => "Description"
            ],
            "Permissions" => [
                "InstallPackage"   => "Determines whether members of the group are allowed to install Packages",
                "UninstallPackage" => "Determines whether members of the group are allowed to uninstall Packages"
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        //Install Module.
        /** @var \Modules\Packages $Packages */
        $Packages = \vDesk\Modules::Packages();
        
        $Packages->Commands->Add(
            new Command(
                null,
                $Packages,
                "Installed",
                true,
                false
            )
        );
        $Packages->Commands->Add(
            new Command(
                null,
                $Packages,
                "Create",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Package", Type::String, false, false),
                    new Parameter(null, null, "Path", Type::String, true, true),
                    new Parameter(null, null, "Compression", Type::Int, true, true)
                ])
            )
        );
        $Packages->Commands->Add(
            new Command(
                null,
                $Packages,
                "Install",
                true,
                true,
                null,
                new Collection([new Parameter(null, null, "Package", Extension\Type::File, false, false)])
            )
        );
        $Packages->Commands->Add(
            new Command(
                null,
                $Packages,
                "Uninstall",
                true,
                true,
                null,
                new Collection([new Parameter(null, null, "Package", Type::String, false, false)])
            )
        );
        $Packages->Save();
        
        //Create permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::CreatePermission("InstallPackage", false);
        $Security::CreatePermission("UninstallPackage", false);
        
        //Extract files.
        self::Deploy($Phar, $Path);
    }
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        
        //Uninstall Module.
        /** @var \Modules\Packages $Packages */
        $Packages = \vDesk\Modules::Packages();
        $Packages->Delete();
        
        //Delete permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::DeletePermission("InstallPackage");
        $Security::DeletePermission("UninstallPackage");
        
        //Delete files.
        self::Undeploy();
        
    }
}