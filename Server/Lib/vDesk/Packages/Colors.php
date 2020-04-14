<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Locale\IPackage;
use vDesk\Package;

/**
 * Colors Package
 *
 * @package vDesk\Packages
 */
class Colors extends Package implements IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "Colors";
    
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
    public const Description = "Package providing functionality for changing the \"look&feel\" of vDesk.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = [
        "Configuration" => "1.0.0",
        "Locale"        => "1.0.0"
    ];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design => [
                "vDesk/Colors"
            ],
            self::Lib    => [
                "vDesk/Colors.js",
                "vDesk/Colors"
            ]
        ]
    ];
    
    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "Colors" => [
                "Foreground"    => "Vordergrund",
                "Background"    => "Hintergrund",
                "Border"        => "Rahmen",
                "Light"         => "Hell",
                "Dark"          => "Dunkel",
                "Font"          => "Schriftart",
                "Color"         => "Farbe",
                "Colors"        => "Farben",
                "Disabled"      => "Deaktiviert",
                "Control"       => "Steuerelement",
                "Button"        => "Schaltfläche",
                "TextBox"       => "Textfeld",
                "Selected"      => "Selektiert",
                "Hover"         => "Hover",
                "Press"         => "Druck",
                "Error"         => "Fehler"
            ]
        ],
        "EN" => [
            "Colors" => [
                "Foreground"    => "Foreground",
                "Background"    => "Background",
                "Border"        => "Border",
                "Light"         => "Light",
                "Dark"          => "Dark",
                "Font"          => "Font",
                "Color"         => "Color",
                "Colors"        => "Colors",
                "Disabled"      => "Disabled",
                "Control"       => "Control",
                "Button"        => "Button",
                "TextBox"       => "Textbox",
                "Selected"      => "Selected",
                "Hover"         => "Hover",
                "Press"         => "Press",
                "Error"         => "Error"
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        //Extract files.
        static::Deploy($Phar, $Path);
    }
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        //Delete files.
        static::Undeploy();
    }
}