<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\DataProvider\Expression;
use vDesk\Packages\Colors as Package;

/**
 * Colors Update manifest class.
 *
 * @package vDesk\Colors
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Colors extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = Package::class;

    /**
     * The required Package version of the Update.
     */
    public const RequiredVersion = "1.1.0";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Reworked color configuration facade.
- Added dropdown menu with color presets.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy => [
            Package::Client => [
                Package::Design => [
                    "vDesk/Colors/Configuration.css"
                ],
                Package::Lib    => [
                    "vDesk/Colors.js",
                    "vDesk/Colors/Configuration.js"
                ]
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {
        //Update files.
        self::Deploy($Phar, $Path);

        //Add new translations.
        Expression::Insert()
                  ->Into("Locale.Translations")
                  ->Values(
                      ["Locale" => "DE", "Domain" => "Colors", "Tag" => "Presets", "Value" => Package::Locale["DE"]["Colors"]["Presets"]],
                      ["Locale" => "EN", "Domain" => "Colors", "Tag" => "Presets", "Value" => Package::Locale["EN"]["Colors"]["Presets"]],
                      ["Locale" => "NL", "Domain" => "Colors", "Tag" => "Presets", "Value" => Package::Locale["NL"]["Colors"]["Presets"]]
                  )
                  ->Execute();
    }
}