<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Configuration\Settings;
use vDesk\Pages\IPackage;

/**
 * Class Documentation
 *
 * @package vDesk\Packages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Documentation extends Package implements IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "Documentation";
    
    /**
     * The version of the Package.
     */
    public const Version = "1.0.2";
    
    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";
    
    /**
     * The name of the Package.
     */
    public const Description = "Package that provides a documentation website.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Pages" => "1.1.0"];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Server => [
            self::Lib         => [
                "vDesk/Documentation"
            ],
            self::Modules     => [
                "Documentation.php"
            ],
            self::Pages       => [
                "Documentation.php",
                "Documentation"
            ],
            self::Templates   => [
                "Documentation.php",
                "Documentation"
            ],
            self::Stylesheets => [
                "Documentation"
            ],
            self::Images      => [
                "Documentation"
            ],
            self::Scripts     => [
                "Controls.js"
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        //Create routes.
        Settings::$Local["Routes"]["/Documentation/Category/Client/Topic/{Topic}"] = [
            "Module"  => "Documentation",
            "Command" => "ClientPage"
        ];
        Settings::$Local["Routes"]["/Documentation/Category/Client"]               = [
            "Module"  => "Documentation",
            "Command" => "ClientPages"
        ];
        Settings::$Local["Routes"]["/Documentation/Category/Server/Topic/{Topic}"] = [
            "Module"  => "Documentation",
            "Command" => "ServerPage"
        ];
        Settings::$Local["Routes"]["/Documentation/Category/Server"]               = [
            "Module"  => "Documentation",
            "Command" => "ServerPages"
        ];
        Settings::$Local["Routes"]["/Documentation/Topic/{Topic}"]                 = [
            "Module"  => "Documentation",
            "Command" => "Topic"
        ];
        
        //Extract files.
        self::Deploy($Phar, $Path);
        
    }
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        self::Undeploy();
    }
    
}