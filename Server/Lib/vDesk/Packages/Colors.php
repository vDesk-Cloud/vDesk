<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Locale\IPackage;

/**
 * Colors Package manifest class.
 *
 * @package vDesk\Packages
 */
final class Colors extends Package implements IPackage {

    /**
     * The name of the Package.
     */
    public const Name = "Colors";

    /**
     * The version of the Package.
     */
    public const Version = "1.2.0";

    /**
     * The vendor of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";

    /**
     * The description of the Package.
     */
    public const Description = "Package providing functionality for changing the \"look&feel\" of vDesk.";

    /**
     * The dependencies of the Package.
     */
    public const Dependencies = [
        "Configuration" => "1.1.0",
        "Locale"        => "1.1.0"
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
                "Presets"    => "Voreinstellungen",
                "Foreground" => "Vordergrund",
                "Background" => "Hintergrund",
                "Border"     => "Rahmen",
                "Light"      => "Hell",
                "Dark"       => "Dunkel",
                "Font"       => "Schriftart",
                "Color"      => "Farbe",
                "Colors"     => "Farben",
                "Disabled"   => "Deaktiviert",
                "Control"    => "Steuerelement",
                "Button"     => "Schaltfläche",
                "TextBox"    => "Textfeld",
                "Selected"   => "Selektiert",
                "Hover"      => "Hover",
                "Press"      => "Druck",
                "Error"      => "Fehler"
            ]
        ],
        "EN" => [
            "Colors" => [
                "Presets"    => "Presets",
                "Foreground" => "Foreground",
                "Background" => "Background",
                "Border"     => "Border",
                "Light"      => "Light",
                "Dark"       => "Dark",
                "Font"       => "Font",
                "Color"      => "Color",
                "Colors"     => "Colors",
                "Disabled"   => "Disabled",
                "Control"    => "Control",
                "Button"     => "Button",
                "TextBox"    => "Textbox",
                "Selected"   => "Selected",
                "Hover"      => "Hover",
                "Press"      => "Press",
                "Error"      => "Error"
            ]
        ],
        "NL" => [
            "Colors" => [
                "Presets"    => "Voorinstellingen",
                "Foreground" => "Voorgrond",
                "Background" => "Achtergrond",
                "Border"     => "Kader",
                "Light"      => "Licht",
                "Dark"       => "Donker",
                "Font"       => "Lettertype",
                "Color"      => "Kleur",
                "Colors"     => "Kleuren",
                "Disabled"   => "Uitgeschakeld",
                "Control"    => "Besturingselement",
                "Button"     => "Knop",
                "TextBox"    => "Tekstvak",
                "Selected"   => "Geselecteerde",
                "Hover"      => "Hover",
                "Press"      => "Druk op",
                "Error"      => "Fout"
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {
        //Extract files.
        self::Deploy($Phar, $Path);
    }

    /** @inheritDoc */
    public static function Uninstall(string $Path): void {
        //Delete files.
        self::Undeploy();
    }
}