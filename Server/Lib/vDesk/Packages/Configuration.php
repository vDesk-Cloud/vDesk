<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Collation;
use vDesk\DataProvider\Type;
use vDesk\IO\Directory;
use vDesk\IO\Path;
use vDesk\Locale\IPackage;
use vDesk\Modules\Module\Command;
use vDesk\Modules\Module\Command\Parameter;
use vDesk\Struct\Collections\Observable\Collection;

/**
 * Configuration Package manifest class.
 *
 * @package vDesk\Packages\Packages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Configuration extends Package implements IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "Configuration";
    
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
    public const Description = "Package providing functionality for managing local and system configuration settings.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Locale" => "1.0.0"];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design => [
                "vDesk/Configuration"
            ],
            self::Lib    => [
                "vDesk/Configuration.js",
                "vDesk/Configuration"
            ]
        ],
        self::Server => [
            self::Modules => [
                "Configuration.php"
            ],
            self::Lib     => [
                "vDesk/Configuration"
            ]
        ]
    ];
    
    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "Configuration" => [
                "Administration"            => "Administration",
                "AdministrationDescription" => "Hier haben Sie die Möglichkeit, systemweite Einstellungen zu treffen.",
                
                "Entry"                     => "Eintrag",
                "Log"                       => "Log",
                "Module"                    => "Konfiguration",
                "Presentation"              => "Darstellung",
                "Settings"                  => "Einstellungen",
                "SettingsDescription"       => "Benutzereinstellungen. Hier haben Sie die Möglichkeit, persönliche Einstellungen zu treffen und das Aussehen sowie das Verhalten von vDesk zu personalisieren.",
                "SystemSettings"            => "Systemeinstellungen"
            ],
            "Permissions"   => [
                "ReadSettings"   => "Legt fest ob Mitglieder der Gruppe Systemeinstellungen sehen können",
                "UpdateSettings" => "Legt fest ob Mitglieder der Gruppe Systemeinstellungen bearbeiten können"
            ]
        ],
        "EN" => [
            "Configuration" => [
                "Administration"            => "Administration",
                "AdministrationDescription" => "System related settings. Here you have the possibillity to manage system wide settings.",
                "Entry"                     => "Entry",
                "Log"                       => "Log",
                "Module"                    => "Configuration",
                "Presentation"              => "Presentation",
                "Settings"                  => "Settings",
                "SettingsDescription"       => "Usersettings. Here you have the possibility to set personal settings and personalize the look and feel of vDesk.",
                "SystemInformations"        => "System informations",
                "SystemSettings"            => "System settings"
            ],
            "Permissions"   => [
                "ReadSettings"   => "Determines whether members of the group are allowed to see system settings",
                "UpdateSettings" => "Determines whether members of the group are allowed to update system settings"
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        Expression::Create()
                  ->Schema("Configuration")
                  ->Execute();
        
        //Create tables.
        Expression::Create()
                  ->Table(
                      "Configuration.Settings",
                      [
                          "Domain"    => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Tag"       => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Value"     => ["Type" => Type::TinyText, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null],
                          "Type"      => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Nullable"  => ["Type" => Type::Boolean, "Default" => false],
                          "Public"    => ["Type" => Type::Boolean, "Default" => false],
                          "Validator" => ["Type" => Type::Text, "Collation" => Collation::UTF8 | Collation::Binary, "Nullable" => true, "Default" => null]
                      ],
                      [
                          "Primary" => ["Fields" => ["Domain" => 255, "Tag" => 255]]
                      ]
                  )
                  ->Execute();
        
        //Install Module.
        /** @var \Modules\Configuration $Configuration */
        $Configuration = \vDesk\Modules::Configuration();
        $Configuration->Commands->Add(
            new Command(
                null,
                $Configuration,
                "GetSystemInfo",
                true,
                false
            )
        );
        $Configuration->Commands->Add(
            new Command(
                null,
                $Configuration,
                "GetSettings",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "All", \vDesk\Struct\Type::Bool, true, false)])
            )
        );
        $Configuration->Commands->Add(
            new Command(
                null,
                $Configuration,
                "UpdateSetting",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Domain", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Tag", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Value", \vDesk\Struct\Type::String, false, true)
                ])
            )
        );
        $Configuration->Commands->Add(
            new Command(
                null,
                $Configuration,
                "GetLog",
                true,
                true
            )
        );
        $Configuration->Commands->Add(
            new Command(
                null,
                $Configuration,
                "ClearLog",
                true,
                false
            )
        );
        $Configuration->Save();
        
        //Create settings directory.
        Directory::Create($Path . Path::Separator . self::Server . Path::Separator . "Settings");
        
        //Extract files.
        self::Deploy($Phar, $Path);
        
    }
    
    /**
     * @inheritDoc
     */
    public static function PostInstall(\Phar $Phar, string $Path): void {
        Settings::$Local->Save($Path . Path::Separator . self::Server . Path::Separator . "Settings");
        Settings::$Remote->Save(true);
        
        //Create permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::CreatePermission("ReadSettings", false);
        $Security::CreatePermission("UpdateSettings", false);
    }
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        
        //Uninstall Module.
        /** @var \Modules\Configuration $Configuration */
        $Configuration = \vDesk\Modules::Configuration();
        $Configuration->Delete();
        
        //Delete permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::DeletePermission("ReadSettings");
        $Security::DeletePermission("UpdateSettings");
        
        //Drop database.
        Expression::Drop()
                  ->Schema("Configuration")
                  ->Execute();
        
        //Delete files.
        Directory::Delete($Path . Path::Separator . self::Server . Path::Separator . "Settings", true);
        self::Undeploy();
        
    }
}