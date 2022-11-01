<?php
declare(strict_types=1);

namespace vDesk\Locale;

use vDesk\DataProvider\Expression;
use vDesk\Security\User;
use vDesk\Struct\Collections\Dictionary;

/**
 * Class LocaleDictionary represents ...
 *
 * @package vDesk\Locale
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class LocaleDictionary extends Dictionary {
    
    /**
     * Indicator for undefined translations.
     */
    public const UndefinedTranslation = "[Undefined Translation]";
    
    /**
     * @inheritdoc
     */
    public function offsetGet($Key) {
        
        // Check if the specified key is a locale-identifier and load the locale.
        if(\strlen($Key) === 2 && \ctype_upper($Key)) {
            if($this->offsetExists($Key)) {
                return parent::offsetGet($Key);
            }
            $this->LoadLocale($Key);
            return parent::offsetGet($Key);
        }
        
        if($this->offsetExists(User::$Current->Locale)) {
            return parent::offsetGet(User::$Current->Locale)[$Key];
        }
        
        $this->LoadDomain(User::$Current->Locale, $Key);
        return parent::offsetGet(User::$Current->Locale)[$Key];
    }
    
    /**
     * Loads the translations of a specified locale and domain.
     *
     * @param string $Locale The locale to load the translations of.
     * @param string $Domain The domain to load the translations of.
     */
    public function LoadDomain(string $Locale, string $Domain): void {
        $Result = Expression::Select("Tag", "Value")
                            ->From("Locale.Translations")
                            ->Where([
                                "Locale" => $Locale,
                                "Domain" => $Domain
                            ])
                            ->Execute();
        if($Result->Count > 0) {
            $Translations = [];
            foreach($Result as $Row) {
                $Translations[$Row["Tag"]] = $Row["Value"];
            }
            $this->Add($Locale, new DomainDictionary([$Domain => new TranslationDictionary($Translations)]));
        } else {
            $this->Add($Locale, new DomainDictionary([$Domain => new EmptyDictionary(self::UndefinedTranslation)]));
        }
    }
    
    /**
     * Loads the translations of a specified local.
     *
     * @param string $Locale The locale to load the translations of.
     */
    public function LoadLocale(string $Locale): void {
        $Result = Expression::Select("*")
                            ->From("Translations")
                            ->Where(["Locale" => $Locale])
                            ->Execute();
        if($Result->Count > 0) {
            $Domains = [];
            foreach($Result as $Row) {
                if(!isset($Domains[$Row["Domain"]])) {
                    $Domains[$Row["Domain"]] = [];
                }
                $Domains[$Row["Domain"]][$Row["Tag"]] = $Row["Value"];
            }
            $DomainDictionary = new DomainDictionary();
            foreach($Domains as $sDomain => $Translations) {
                $TranslationDictionary = new TranslationDictionary();
                foreach($Translations as $Key => $Value) {
                    $TranslationDictionary->Add($Key, $Value);
                }
                $DomainDictionary->Add($sDomain, $TranslationDictionary);
            }
            $this->Add($Locale, $DomainDictionary);
        } else {
            $this->Add($Locale, new EmptyDictionary(new EmptyDictionary(self::UndefinedTranslation)));
        }
    }
    
}