<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
use vDesk\IO\File;
use vDesk\IO\FileInfo;
use vDesk\IO\Path;
use vDesk\Modules\Command;
use vDesk\Modules\Module;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Security\User;
use vDesk\Struct\Text;
use vDesk\Updates\Update;
use vDesk\Utils\Log;

/**
 * UpdateHost Module.
 *
 * @package vDesk\Updates
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class UpdateHost extends Module {

    /**
     * Gets every available Update for a specified set of Packages.
     *
     * @param null|array $Packages The Packages to check for Updates.
     *
     * @return array An array containing every available Update for the specified Packages.
     */
    public static function Available(array $Packages = null): array {
        $Packages   ??= Command::$Parameters["Packages"];
        $Conditions = [];

        foreach($Packages as $Package => $Version) {
            [$Major, $Minor, $Patch] = \explode(".", $Version);
            $Conditions[] = ["Package" => $Package, "Major" => $Major, "Minor" => [">=" => $Minor], "Patch" => [">=" => $Patch]];
        }

        $Updates = [];
        foreach(
            Expression::Select("*")
                      ->From("Updates.Hosted")
                      ->Where(...$Conditions)
                      ->OrderBy(["Package" => true, "Version" => true])
            as
            $Update
        ) {
            $Updates[] = [
                "Package"         => $Update["Package"],
                "Version"         => $Update["Version"],
                "RequiredVersion" => "{$Update["Major"]}.{$Update["Minor"]}.{$Update["Patch"]}",
                "Hash"            => $Update["Hash"],
                "Dependencies"    => \json_decode($Update["Dependencies"]),
                "Vendor"          => $Update["Vendor"],
                "Description"     => $Update["Description"]
            ];
        }
        return $Updates;
    }

    /**
     * Gets every hosted Update.
     *
     * @return array The hosted Updates.
     */
    public static function Hosted(): array {
        $Updates = [];
        foreach(
            Expression::Select("*")
                      ->From("Updates.Hosted")
            as
            $Update
        ) {
            $Updates[] = [
                "Package"         => $Update["Package"],
                "Version"         => $Update["Version"],
                "RequiredVersion" => "{$Update["Major"]}.{$Update["Minor"]}.{$Update["Patch"]}",
                "Hash"            => $Update["Hash"],
                "Dependencies"    => \json_decode($Update["Dependencies"]),
                "Vendor"          => $Update["Vendor"],
                "Description"     => $Update["Description"]
            ];
        }
        return $Updates;
    }

    /**
     * Uploads an Update for installation or hosting.
     *
     * @param null|\vDesk\IO\FileInfo $Update The Phar archive of the Update to upload.
     *
     * @return array The uploaded Update.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to install Updates.
     */
    public static function Host(FileInfo $Update = null): array {
        if(!User::$Current->Permissions["InstallUpdate"]) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to host Update without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Update ??= Command::$Parameters["Update"];

        //Save Update file.
        $File       = \uniqid("update", true) . ".phar";
        $Path       = Settings::$Local["UpdateHost"]["Directory"] . Path::Separator . $File;
        $TargetFile = File::Create($Path);
        $TempFile   = $Update->Open();
        while(!$TempFile->EndOfStream()) {
            $TargetFile->Write($TempFile->Read());
        }

        //Delete temp file.
        $Update->Delete();

        $Phar = new \Phar($Path);

        //Wire autoload to PHAR archive.
        \vDesk::$Load[] = static fn(string $Class): string => "phar://{$Phar->getPath()}/Server/Lib/" . Text::Replace($Class, "\\", "/") . ".php";
        \vDesk::$Load[] = static fn(string $Class): string => "phar://{$Phar->getPath()}/Server/" . Text::Replace($Class, "\\", "/") . ".php";

        //Load Update.
        /** @var \vDesk\Updates\Update $Update */
        /** @var \vDesk\Packages\Package $Package */
        [$UpdateManifest, $Package] = include $Phar->getPath();
        [$Major, $Minor, $Patch] = \explode(".", $UpdateManifest::RequiredVersion);

        //Check if a similar Update already exists.
        if(
            Expression::Select("1")
                      ->From("Updates.Hosted")
                      ->Where([
                          "Package" => $Package::Name,
                          "Version" => $Package::Version,
                          "Major"   => (int)$Major,
                          "Minor"   => (int)$Minor,
                          "Patch"   => (int)$Patch
                      ])
                      ->Execute()
                ->Count > 0
        ) {
            File::Delete($Path);
            throw new \InvalidArgumentException(
                "Update for Package \"" . $Package::Name . "\" from version " . $UpdateManifest::RequiredVersion . " to " . $Package::Version . " already exists!"
            );
        }

        //Save Update manifest.
        Expression::Insert()
                  ->Into("Updates.Hosted")
                  ->Values([
                      "Package"      => $Package::Name,
                      "Version"      => $Package::Version,
                      "Major"        => (int)$Major,
                      "Minor"        => (int)$Minor,
                      "Patch"        => (int)$Patch,
                      "Hash"         => $Phar->getSignature()["hash"],
                      "File"         => $File,
                      "Dependencies" => \json_encode($Package::Dependencies),
                      "Vendor"       => $Package::Vendor,
                      "Description"  => $UpdateManifest::Description
                  ])
                  ->Execute();

        return ["Hash" => $Phar->getSignature()["hash"]] + $UpdateManifest->ToDataView();
    }

    /**
     * Downloads the file of a specified Update.
     *
     * @param null|string $Hash The hash of the Update to download.
     *
     * @return FileInfo True if the specified Update has been successfully deleted; otherwise, false.
     */
    public static function Download(string $Hash = null): FileInfo {
        $Hash ??= Command::$Parameters["Hash"];
        $File = Expression::Select("File")
                          ->From("Updates.Hosted")
                          ->Where(["Hash" => $Hash])();
        if($File === null) {
            throw new \InvalidArgumentException("Update with hash \"{$Hash}\" doesn't exist!");
        }
        $Update           = new FileInfo(Settings::$Local["UpdateHost"]["Directory"] . Path::Separator . $File);
        $Update->MimeType = Update::MimeType;
        return $Update;
    }

    /**
     * Deletes a hosted Update.
     *
     * @param null|string $Hash The hash of the Update to delete.
     *
     * @return bool True if the specified Update has been successfully deleted; otherwise, false.
     */
    public static function Remove(string $Hash = null): bool {
        $Hash ??= Command::$Parameters["Hash"];

        //Check if the Update exists.
        $File = Expression::Select("File")
                          ->From("Updates.Hosted")
                          ->Where(["Hash" => $Hash])();
        if($File === null) {
            return false;
        }

        //Delete Update.
        Expression::Delete()
                  ->From("Updates.Hosted")
                  ->Where(["Hash" => $Hash])
                  ->Execute();
        File::Delete(Settings::$Local["UpdateHost"]["Directory"] . Path::Separator . $File);
        return true;
    }
}