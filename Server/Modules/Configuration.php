<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
use vDesk\Modules\Command;
use vDesk\IO\FileStream;
use vDesk\IO\Stream;
use vDesk\Modules\Module;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Security\User;
use vDesk\Utils\Convert;
use vDesk\Utils\Log;
use vDesk\Utils\Validate;

/**
 * Configuration Module.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
final class Configuration extends Module {

    /**
     * Gets the system settings of vDesk.
     *
     * @param bool|null $All Flag indicating whether to return all settings instead of just client visible settings.
     *
     * @return array An array containing the system settings of vDesk.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to read system settings.
     */
    public static function GetSettings(bool $All = null): array {
        if($All ?? Command::$Parameters["All"] ?? false) {
            if(!User::$Current->Permissions["ReadSettings"]) {
                Log::Warn(__METHOD__, User::$Current->Name . " tried to view system settings without having permissions.");
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
                $Setting["Validator"]           = \json_decode($Setting["Validator"] ?? "null");
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
     * @param null|string       $Domain    The domain of the Setting.
     * @param null|string       $Tag       The tag of the Setting.
     * @param mixed             $Value     The value of the Setting.
     * @param string|null       $Type      The type of the Setting.
     * @param bool              $Nullable  Flag indicating whether the value of the Setting is nullable.
     * @param bool              $Public    Flag indicating whether the value of the Setting is public visible.
     * @param null|array|object $Validator The validator of the Setting.
     *
     * @return Settings\Remote\Setting The created configuration Setting.
     */
    public static function CreateSetting(
        string            $Domain = null,
        string            $Tag = null,
        mixed             $Value = null,
        string            $Type = null,
        bool              $Nullable = false,
        bool              $Public = false,
        array|object|null $Validator = null
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
     * @param null|string $Domain The domain the setting is grouped by.
     * @param null|string $Tag    The identifier-tag of the setting.
     * @param null|mixed  $Value  The value of the setting.
     *
     * @return bool True if the setting has been successfully updated.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current user is not allowed to edit system settings.
     * @throws \TypeError Thrown if the value doesn't match the required type or validator of the setting.
     */
    public static function UpdateSetting(string $Domain = null, string $Tag = null, mixed $Value = null): bool {
        if(!User::$Current->Permissions["UpdateSettings"]) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to edit system settings without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Domain ??= Command::$Parameters["Domain"];
        $Tag    ??= Command::$Parameters["Tag"];
        $Value  ??= Command::$Parameters["Value"];

        [$Type, $Validator] = Expression::Select("Type", "Validator")
                                        ->From("Configuration.Settings")
                                        ->Where([
                                            "Domain" => $Domain,
                                            "Tag"    => $Tag
                                        ])
                                        ->Execute()
                                        ->ToArray();

        if(!Validate::As($Value, $Type, \json_decode($Validator ?? "null"))) {
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
     * @param null|string $Domain The domain of the Setting.
     * @param null|string $Tag    The tag of the Setting.
     *
     * @return bool The created configuration Setting.
     */
    public static function DeleteSetting(string $Domain = null, string $Tag = null): bool {
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
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current user is not allowed to see system settings.
     */
    public static function GetLog(): FileStream {
        if(!User::$Current->Permissions["ReadSettings"]) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to view log without having permissions.");
            throw new UnauthorizedAccessException();
        }
        return new FileStream(Settings::$Local["Log"]["Target"], Stream\Mode::Read | Stream\Mode::Binary);
    }

    /**
     * Clears the contents of the log-file.
     *
     * @return bool True on success.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current user is not allowed to edit system settings.
     */
    public static function ClearLog(): bool {
        if(!User::$Current->Permissions["UpdateSettings"]) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to clear log without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Stream = new FileStream(Settings::$Local["Log"]["Target"], Stream\Mode::Append);
        $Stream->Truncate(0);
        return true;
    }

}
