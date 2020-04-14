<?php
declare(strict_types=1);

namespace Modules;

use vDesk\DataProvider\Expression;
use vDesk\Modules\Command;
use vDesk\Locale\Countries;
use vDesk\Locale\IPackage;
use vDesk\Modules\Module;
use vDesk\Package;
use vDesk\Package\IModule;
use vDesk\Utils\Log;

/**
 * Class Locale represents ...
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
final class Locale extends Module implements IModule {
    
    /**
     * Gets a Collection of all existing Countries.
     *
     * @param string|null $Locale The locale of the translation of the Countries.
     *
     * @return \vDesk\Locale\Countries A collection of all existing Countries.
     */
    public static function GetCountries(string $Locale = null): Countries {
        return Countries::All($Locale ?? Command::$Parameters["Locale"]);
    }
    
    /**
     * Gets all installed locales.
     *
     * @return array An array containing the country key of all installed locales.
     */
    public static function GetLocales(): array {
        $Locales = [];
        foreach(
            Expression::Select()
                      ->Distinct("Locale")
                      ->From("Locale.Translations")
            as
            $Row
        ) {
            $Locales[] = $Row["Locale"];
        }
        return $Locales;
    }
    
    /**
     * Gets the value of a single translation specified by a locale, domain and key.
     *
     * @param string|null $Locale The locale of the translation.
     * @param string|null $Domain The domain of the translation.
     * @param string|null $Tag    The tag of the translation.
     *
     * @return string The value of the specified translation.
     */
    public static function GetTranslation(string $Locale = null, string $Domain = null, string $Tag = null): string {
        return Expression::Select("*")
                         ->From("Locale.Translations")
                         ->Where([
                             "Locale" => $Locale ?? Command::$Parameters["Locale"],
                             "Domain" => $Domain ?? Command::$Parameters["Domain"],
                             "Tag"    => $Tag ?? Command::$Parameters["Tag"]
                         ])
                         ->Execute()
                         ->ToMap()["Value"] ?? "[Undefined Translation]";
    }
    
    /**
     * Gets all translations of a specified locale and domain.
     *
     * @param string|null $Locale The locale of the translation.
     * @param string|null $Domain The Domain of the translation.
     *
     * @return array The translations of the specified domain.
     */
    public static function GetDomain(string $Locale = null, string $Domain = null): array {
        $Translations = [];
        foreach(
            Expression::Select("*")
                      ->From("Locale.Translations")
                      ->Where([
                          "Locale" => $Locale ?? Command::$Parameters["Locale"],
                          "Domain" => $Domain ?? Command::$Parameters["Domain"]
                      ])
            as
            $Row
        ) {
            $Translations[$Row["Tag"]] = $Row["Value"];
        }
        return $Translations;
    }
    
    /**
     * Gets all translations of a specified locale.
     *
     * @param string|null $Locale The locale of the translation.
     *
     * @return array The translations of the specified locale.
     */
    public static function GetLocale(string $Locale = null): array {
        $Translations = [];
        foreach(
            Expression::Select("*")
                      ->From("Locale.Translations")
                      ->Where(["Locale" => $Locale ?? Command::$Parameters["Locale"]])
            as
            $Row
        ) {
            if(!isset($Translations[$Row["Domain"]])) {
                $Translations[$Row["Domain"]] = [];
            }
            $Translations[$Row["Domain"]][$Row["Tag"]] = $Row["Value"];
        }
        return $Translations;
    }
    
    /**
     * Updates a localized Package.
     *
     * @param \vDesk\Package $Package
     */
    public static function Update(Package $Package): void {
    
    }

    
    /**
     * Installs a localized Package.
     *
     * @param \vDesk\Package $Package The Package to install.
     * @param \Phar          $Phar    The Phar archive of the Package.
     * @param string         $Path    The installation path of the Package.
     */
    public static function Install(Package $Package, \Phar $Phar, string $Path): void {
        if($Package instanceof IPackage) {
            foreach($Package::Locale as $Locale => $Domains) {
                $Expression = Expression::Insert()
                                        ->Into(
                                            "Locale.Translations",
                                            ["Locale", "Domain", "Tag", "Value"]
                                        );
                $Values     = [];
                foreach($Domains as $Domain => $Translations) {
                    foreach($Translations as $Tag => $Value) {
                        $Values[] = [$Locale, $Domain, $Tag, $Value];
                    }
                }
                $Expression->Values(...$Values)
                           ->Execute();
            }
            Log::Info(__METHOD__, "Successfully installed translations of Package '" . $Package::Name . "' (v" . $Package::Version . ").");
        }
    }
    
    /**
     * Uninstalls a localized Package.
     *
     * @param \vDesk\Package $Package The Package to uninstall.
     * @param string         $Path    The installation path of the Package.
     */
    public static function Uninstall(Package $Package, string $Path): void {
        if($Package instanceof IPackage) {
            foreach($Package::Locale as $Locale => $Domains) {
                foreach(\array_keys($Domains) as $Domain) {
                    Expression::Delete()
                              ->From("Locale.Translations")
                              ->Where([
                                  "Locale" => $Locale,
                                  "Domain" => $Domain
                              ])
                              ->Execute();
                }
            }
        }
    }
    
}