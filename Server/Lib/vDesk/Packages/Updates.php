<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Configuration\Settings;
use vDesk\Locale\IPackage;
use vDesk\Modules\Module\Command;
use vDesk\Modules\Module\Command\Parameter;
use vDesk\Struct\Collections\Observable\Collection;
use vDesk\Struct\Type;
use vDesk\Struct\Extension;

/**
 * Updates Package manifest.
 *
 * @package vDesk\Packages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Updates extends Package implements IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "Updates";
    
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
    public const Description = "Package providing functionality for creating setups and creating, installing and uninstalling packages.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Packages" => "1.0.1"];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design => [
                "vDesk/Updates"
            ],
            self::Lib    => [
                "vDesk/Updates.js",
                "vDesk/Updates"
            ]
        ],
        self::Server => [
            self::Modules => [
                "Updates.php"
            ],
            self::Lib     => [
                "vDesk/Updates"
            ]
        ]
    ];
    
    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "Updates"     => [
                "Search"          => "Nach updates suchen",
                "Upload"          => "Hochladen",
                "Download"        => "Herunterladen",
                "Source"          => "Quelle",
                "Updates"         => "Updates",
                "Hash"            => "Hash",
                "Deploy"          => "Bereitstellen",
                "RequiredVersion" => "Vorrausgesetzte Version"
            ],
            "Permissions" => [
                "InstallUpdate" => "Legt fest ob Mitglieder der Gruppe Updates installieren können"
            ]
        ],
        "EN" => [
            "Updates"     => [
                "Search"          => "Search for updates",
                "Upload"          => "Upload",
                "Download"        => "Download",
                "Source"          => "Source",
                "Updates"         => "Updates",
                "Hash"            => "Hash",
                "Deploy"          => "Deploy",
                "RequiredVersion" => "Required version"
            ],
            "Permissions" => [
                "InstallUpdate" => "Determines whether members of the group are allowed to install Updates"
            ]
        ],
        "NL" => [
            "Updates"     => [
                "Search"          => "Zoeken naar updates",
                "Upload"          => "Upload",
                "Download"        => "Download",
                "Source"          => "Bron",
                "Updates"         => "Updates",
                "Hash"            => "Hash",
                "Deploy"          => "Inzetten",
                "RequiredVersion" => "Vereiste versie"
            ],
            "Permissions" => [
                "InstallUpdate" => "Bepaalt of leden van de groep updates mogen installeren"
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        //Install Module.
        /** @var \Modules\Updates $Updates */
        $Updates = \vDesk\Modules::Updates();
        $Updates->Commands->Add(
            new Command(
                null,
                $Updates,
                "Search",
                true,
                false
            )
        );
        $Updates->Commands->Add(
            new Command(
                null,
                $Updates,
                "Install",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Source", Type::String, false, false),
                    new Parameter(null, null, "Hash", Type::String, false, false)
                ])
            )
        );
        $Updates->Commands->Add(
            new Command(
                null,
                $Updates,
                "Download",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Source", Type::String, false, false),
                    new Parameter(null, null, "Hash", Type::String, false, false)
                ])
            )
        );
        $Updates->Commands->Add(
            new Command(
                null,
                $Updates,
                "Deploy",
                true,
                true,
                null,
                new Collection([new Parameter(null, null, "Update", Extension\Type::File, false, false)])
            )
        );
        $Updates->Commands->Add(
            new Command(
                null,
                $Updates,
                "Create",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Update", Type::String, false, false),
                    new Parameter(null, null, "Path", Type::String, true, true),
                    new Parameter(null, null, "Compression", Type::Int, true, true)
                ])
            )
        );
        
        $Updates->Save();
        
        //Create permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::CreatePermission("InstallUpdate", false);
        
        Settings::$Local["Updates"] = new Settings\Local\Settings([
            "Server" => ["updates.vdesk.cloud"]
        ],
            "Updates"
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
        
        //Delete permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::DeletePermission("InstallUpdate");
        
        //Delete files.
        self::Undeploy();
        
    }
}