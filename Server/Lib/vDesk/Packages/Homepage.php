<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Configuration\Settings;
use vDesk\Pages\IPackage;

/**
 * Homepage Package manifest.
 *
 * @package Homepage
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Homepage extends Package implements IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "Homepage";
    
    /**
     * The version of the Package.
     */
    public const Version = "1.0.1";
    
    /**
     * The vendor of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";
    
    /**
     * The description of the Package.
     */
    public const Description = "Package that provides the vDesk project website.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Documentation" => "1.0.1"];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Server => [
            self::Modules     => [
                "vDesk.php"
            ],
            self::Pages       => [
                "vDesk.php",
                "vDesk"
            ],
            self::Templates   => [
                "vDesk.php",
                "vDesk"
            ],
            self::Stylesheets => [
                "vDesk"
            ],
            self::Images      => [
                "vDesk/",
                "Packages/"
            ],
            self::Scripts     => [
                "vDesk"
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        //Create Package configuration.
        Settings::$Local["Homepage"] = new Settings\Local\Settings(
            ["Recipient" => "email-address"],
            "Homepage"
        );
        
        //Create routes.
        Settings::$Local["Routes"]["Default"]                  = [
            "Module"  => "vDesk",
            "Command" => "Index"
        ];
        Settings::$Local["Routes"]["/vDesk/Page/Contact/Send"] = [
            "Module"  => "vDesk",
            "Command" => "Send"
        ];
        Settings::$Local["Routes"]["/vDesk/Page/{Page}"]       = [
            "Module"  => "vDesk",
            "Command" => "Page"
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