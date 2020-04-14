<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Archive\Element;
use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
use vDesk\Modules\Command;
use vDesk\IO\FileStream;
use vDesk\IO\Stream;
use vDesk\Modules\Module;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Struct\Type;
use vDesk\Utils\Convert;
use vDesk\Utils\Log;
use vDesk\Utils\Validate;

/**
 * Class Settings represents ...
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
final class Configuration extends Module {
    
    /**
     *
     */
    public const System = 0;
    
    /**
     *
     */
    public const Client = 1;
    
    /**
     * Special type for enumeration validated settings.
     */
    public const Enumeration = "enum";
    
    /**
     * The currently supported types of the Setting.
     */
    public const Types = [
        Type::Int,
        Type::Float,
        Type::String,
        Type::Bool,
        Type::Object,
        self::Enumeration,
        \DateTime::class
    ];
    
    /**
     * @param            $Value
     * @param string     $Type
     * @param array|null $Validator
     *
     *
     * @return bool
     */
    public static function Validate($Value, string $Type, ?array $Validator = null): bool {
        switch($Type) {
            case Type::Int:
            case Type::Float:
                
                //Validate type of value.
                if(!Validate::As($Value, $Type)) {
                    return false;
                }
                
                //Validate range if any.
                if(
                    $Validator < ($Validator["Min"] ?? $Validator)
                    || $Validator > ($Validator["Max"] ?? $Validator)
                ) {
                    return false;
                }
                
                return true;
            
            case Type::String:
                //Validate type of value.
                if(!Validate::As($Value, $Type)) {
                    return false;
                }
                
                if(isset($Validator["Expression"])) {
                    return (int)\preg_match($Validator["Expression"], $Value) > 0;
                }
                
                return true;
            
            case self::Enumeration:
                
                //Check if the specified value matches one of the predefined values.
                foreach($Validator ?? [] as $PredefinedValue) {
                    if($Value === $PredefinedValue) {
                        return true;
                    }
                }
                
                return false;
            
            default:
                return Validate::As($Value, $Type);
        }
        
    }
    
    /**
     *
     * @return array
     * @throws \vDesk\Security\UnauthorizedAccessException
     *
     */
    public static function GetSystemInfo(): array {
        if(!\vDesk::$User->Permissions["ReadSettings"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to view system settings without having permissions.");
            throw new UnauthorizedAccessException();
        }
        return [
            "01.03.2015",
            "0.1.2",
            Expression::Select(Expression\Functions::Count("*"))
                      ->From("Archive.Elements")
                      ->Where(["Type" => Element::File])(),
            Expression::Select("COUNT(*)")
                      ->From("Archive.Elements")
                      ->Where(["Type" => Element::Folder])(),
            Expression::Select("SUM(Size)")
                      ->From("Archive.Elements")()
        ];
    }
    
    /**
     * Gets the system settings of vDesk.
     *
     * @param bool|null $All Flag indicating whether to return all settings instead of just client visible settings.
     *
     * @return array An array containing the system settings of vDesk.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to read
     *                                                     system settings.
     *
     */
    public static function GetSettings(bool $All = null): array {
        if($All ?? Command::$Parameters["All"] ?? false) {
            if(!\vDesk::$User->Permissions["ReadSettings"]) {
                Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to view system settings without having permissions.");
                throw new UnauthorizedAccessException();
            }
            $Settings = [];
            foreach(
                Expression::Select("*")
                          ->From("Configuration.Settings")
                          ->OrderBy(["Domain", "Tag"])
                as
                $Setting
            ) {
                $Setting["Value"]               = Convert::To($Setting["Type"], $Setting["Value"]);
                $Setting["Nullable"]            = (bool)$Setting["Nullable"];
                $Setting["Public"]              = (bool)$Setting["Public"];
                $Setting["Validator"]           = $Setting["Validator"] !== null ? \json_decode($Setting["Validator"]) : $Setting["Validator"];
                $Settings[$Setting["Domain"]][] = $Setting;
            }
            return $Settings;
        }
        $Settings = [];
        foreach(
            Expression::Select("*")
                      ->From("Configuration.Settings")
                      ->Where(["Public" => true])
                      ->OrderBy(["Domain", "Tag"])
            as
            $Setting
        ) {
            if(isset($Settings[$Setting["Domain"]])) {
                $Settings[$Setting["Domain"]] += [$Setting["Tag"] => Convert::To($Setting["Type"], $Setting["Value"])];
            } else {
                $Settings[$Setting["Domain"]] = [$Setting["Tag"] => Convert::To($Setting["Type"], $Setting["Value"])];
            }
        }
        return $Settings;
    }
    
    /**
     * Creates a new configuration Setting.
     *
     * @param string      $Domain    The domain of the Setting.
     * @param string      $Tag       The tag of the Setting.
     * @param mixed       $Value     The value of the Setting.
     * @param string|null $Type      The type of the Setting.
     * @param bool        $Nullable  Flag indicating whether the value of the Setting is nullable.
     * @param bool        $Public    Flag indicating whether the value of the Setting is public visible.
     * @param null        $Validator The validator of the Setting.
     *
     * @return Settings\Remote\Setting The created configuration Setting.
     */
    public static function CreateSetting(
        string $Domain = null,
        string $Tag = null,
        $Value = null,
        string $Type = null,
        bool $Nullable = false,
        bool $Public = false,
        $Validator = null
    ): Settings\Remote\Setting {
        Expression::Insert()
                  ->Into("Configuration.Settings")
                  ->Values([
                      "Domain"    => $Domain,
                      "Tag"       => $Tag,
                      "Value"     => $Value,
                      "Type"      => $Type,
                      "Nullable"  => $Nullable,
                      "Public"    => $Public,
                      "Validator" => $Validator
                  ])
                  ->Execute();
        return new Settings\Remote\Setting($Tag, $Value, $Type);
    }
    
    /**
     * Updates the value of a configuration setting.
     *
     * @param string $Domain The domain the setting is grouped by.
     * @param string $Tag    The identifier-tag of the setting.
     * @param mixed  $Value  The value of the setting.
     *
     * @return bool True if the setting has been successfully updated.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current user is not allowed to edit
     *                                                     systemsettings.
     * @throws \Exception
     */
    public static function UpdateSetting($Domain = null, $Tag = null, $Value = null): bool {
        if(!\vDesk::$User->Permissions["UpdateSettings"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to edit system settings without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Domain ??= Command::$Parameters["Domain"];
        $Tag    ??= Command::$Parameters["Tag"];
        $Value  ??= Command::$Parameters["Value"];
        
        [$Type, $Validator] = Expression::Select(
            "Type",
            "Validator"
        )
                                        ->From("Configuration.Settings")
                                        ->Where([
                                            "Domain" => $Domain,
                                            "Tag"    => $Tag
                                        ])
                                        ->Execute()
                                        ->ToArray();
        
        if(!Validate::As($Value, $Type, $Validator !== null ? \json_decode($Validator) : null)) {
            throw new \TypeError("The datatype of setting '[{$Domain}][{$Tag}]' must be type of '{$Type}', '" . \gettype($Value) . "' given.");
        }
        
        Expression::Update("Configuration.Settings")
                  ->Set(["Value" => $Value])
                  ->Where([
                      "Domain" => $Domain,
                      "Tag"    => $Tag
                  ])
                  ->Execute();
        return true;
        
    }
    
    /**
     * Deletes a configuration Setting.
     *
     * @param string $Domain The domain of the Setting.
     * @param string $Tag    The tag of the Setting.
     *
     * @return bool The created configuration Setting.
     */
    public static function DeleteSetting(
        string $Domain = null,
        string $Tag = null
    ): bool {
        Expression::Delete()
                  ->From("Configuration.Settings")
                  ->Where([
                      "Domain" => $Domain,
                      "Tag"    => $Tag
                  ])
                  ->Execute();
        return true;
    }
    
    /**
     * Gets the contents of the log-file.
     *
     * @return \vDesk\IO\FileStream A FileStream pointing to the log file.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current user is not allowed to see system
     *                                                     settings.
     */
    public static function GetLog(): FileStream {
        if(!\vDesk::$User->Permissions["ReadSettings"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to view log without having permissions.");
            throw new UnauthorizedAccessException();
        }
        //Why?
        try {
            $lel = Settings::$Local["Log"]["Target"];
        } catch(\Exception $exception) {
        }
        
        return new FileStream(Settings::$Local["Log"]["Target"], Stream\Mode::Read | Stream\Mode::Binary);
    }
    
    /**
     * Clears the contents of the log-file.
     *
     * @return bool True on success.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current user is not allowed to edit
     *                                                     systemsettings.
     */
    public static function ClearLog(): bool {
        if(!\vDesk::$User->Permissions["UpdateSettings"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to clear log without having permissions.");
            throw new UnauthorizedAccessException();
        }
        //Why?
        try {
            $lel = Settings::$Local["Log"]["Target"];
        } catch(\Exception $exception) {
        }
        $Stream = new FileStream(Settings::$Local["Log"]["Target"], Stream\Mode::Append);
        $Stream->Truncate(0);
        $Stream->Close();
        return true;
    }
    
}
