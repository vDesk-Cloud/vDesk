<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Client;
use vDesk\Configuration\Settings;
use vDesk\Modules\Command;
use vDesk\IO\DirectoryInfo;
use vDesk\IO\File;
use vDesk\IO\FileInfo;
use vDesk\IO\Path;
use vDesk\Modules\Module;
use vDesk\Packages\DependencyException;
use vDesk\Packages\IModule;
use vDesk\Packages\Package;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Security\User;
use vDesk\Struct\Collections\Collection;
use vDesk\Struct\Text;
use vDesk\Utils\Log;

/**
 * Packages Module.
 *
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Packages
 */
final class Packages extends Module {

    /**
     * Gets the description of every installed module.
     *
     * @return array The descriptions of every installed module.
     *
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to install Packages.
     */
    public static function Installed(): array {
        if(!User::$Current->Permissions["InstallPackage"]) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to view installed Packages without having permissions.");
            throw new UnauthorizedAccessException();
        }
        return self::Resolve()
                   ->Map(static fn(Package $Package): array => $Package->ToDataView())
                   ->ToArray();
    }

    /**
     * Creates a Package.
     *
     * @param null|string $Package     The name of the Package to create.
     * @param string|null $Path        The output path of the Package to create.
     * @param null|int    $Compression Optional compression of the Package to create.
     *
     * @return \vDesk\Packages\Package An instance of the Package representing the created Package.
     *
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to install Packages.
     */
    public static function Create(string $Package = null, string $Path = null, int $Compression = null): Package {
        if(!User::$Current->Permissions["InstallPackage"]) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to create Package without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Package  ??= Command::$Parameters["Package"];
        $Packages = Package::Server . "/" . Package::Lib . "/vDesk/Packages";

        //Create Package.phar
        $Phar = (new \Phar(($Path ?? Command::$Parameters["Path"] ?? \Server) . Path::Separator . "{$Package}.phar"));
        $Phar->setSignatureAlgorithm(\Phar::SHA256);
        $Class = "\\vDesk\\Packages\\{$Package}";
        $Phar->startBuffering();
        $Phar->setStub(
            <<<STUB
<?php
    Phar::mapPhar(__FILE__);
    include "phar://" . __FILE__ . "/$Packages/{$Package}.php";
    return new {$Class}();
    __HALT_COMPILER();
STUB
        );

        /** @var \vDesk\Packages\Package $Package */
        $Package = new $Class();
        $Package::Compose($Phar);

        //Bundle Package manifest if not already happened by the Package itself.
        if(!isset($Phar[$Packages])) {
            $Phar->addEmptyDir($Packages);
            $Phar->addFile(
                \Server
                . Path::Separator
                . Package::Lib
                . Path::Separator
                . "vDesk"
                . Path::Separator
                . "Packages"
                . Path::Separator
                . $Package::Name . ".php",
                "{$Packages}/" . $Package::Name . ".php"
            );
        }

        if(($Compression ??= Command::$Parameters["Compression"] ?? \Phar::NONE) !== \Phar::NONE) {
            //Yay, this never gets executed but is necessary to prevent PHP from crying about "phar exists and must be unlinked prior to conversion"...
            if($Compression === \Phar::GZ && File::Exists($Phar->getPath() . ".gz")) {
                File::Delete($Phar->getPath() . ".gz");
            } else if($Compression === \Phar::BZ2 && File::Exists($Phar->getPath() . ".bz")) {
                File::Delete($Phar->getPath() . ".bz");
            }
            $Phar->compress($Compression)->setStub($Phar->getStub());
        }
        $Phar->stopBuffering();

        return $Package;

    }

    /**
     * Installs a specified Package.
     *
     * @param \vDesk\IO\FileInfo|null $Package The Phar archive of the Package to install.
     *
     * @return \vDesk\Packages\Package The installed Package.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to install Packages.
     * @throws \vDesk\Packages\DependencyException Thrown if a dependency of the Package cannot be satisfied.
     */
    public static function Install(FileInfo $Package = null): Package {
        if(!User::$Current->Permissions["InstallPackage"]) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to install Package without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Package ??= Command::$Parameters["Package"];

        //Save Package file.
        $Path       = \sys_get_temp_dir() . Path::Separator . "{$Package->Name}.phar";
        $TargetFile = File::Create($Path);
        $TempFile   = $Package->Open();
        while(!$TempFile->EndOfStream()) {
            $TargetFile->Write($TempFile->Read());
        }
        $TargetFile->Close();
        $TempFile->Close();
        //Delete temp file.
        $Package->Delete();

        $Root = Path::GetFullPath(\Server . Path::Separator . "..");
        $Phar = new \Phar($Path);

        //Wire autoload to PHAR archive.
        \vDesk::$Load[] = static fn(string $Class): string => "phar://{$Phar->getPath()}/Server/Lib/" . Text::Replace($Class, "\\", "/") . ".php";
        \vDesk::$Load[] = static fn(string $Class): string => "phar://{$Phar->getPath()}/Server/" . Text::Replace($Class, "\\", "/") . ".php";

        //Load Package.
        /** @var \vDesk\Packages\Package $Package */
        $Package = include $Phar->getPath();

        //Validate dependencies.
        $Packages = self::Resolve();
        foreach($Package::Dependencies as $Dependency => $Version) {
            $DependencyPackage = $Packages->Find(fn(Package $Installed): bool => $Installed::Name === $Dependency);
            if($DependencyPackage === null) {
                throw new DependencyException("Missing dependency package '{$Dependency}' in version '{$Version}'!");
            }
            if(\version_compare($DependencyPackage::Version, $Version) < 0) {
                throw new DependencyException("Requiring dependency package '{$Dependency}' in version '{$Version}', but installed '" . $DependencyPackage::Version . "'!");
            }
        }

        //Install Package.
        \vDesk::$Phar = true;
        $Package::PreInstall($Phar, $Root);
        $Package::Install($Phar, $Root);
        $Package::PostInstall($Phar, $Root);

        //Deploy Package manifest file if not already happened by the Package itself.
        $Manifest = \Server
                    . Path::Separator
                    . Package::Lib
                    . Path::Separator
                    . "vDesk"
                    . Path::Separator
                    . "Packages"
                    . Path::Separator
                    . $Package::Name . ".php";
        if(!File::Exists($Manifest)) {
            File::Copy(
                "phar://{$Phar->getPath()}/" . Package::Server . "/" . Package::Lib . "/vDesk/Packages/" . $Package::Name . ".php",
                $Manifest
            );
        }

        //Install specialized Packages.
        foreach(\vDesk\Modules::RunAll() as $Module) {
            if($Module instanceof IModule) {
                $Module::Install($Package, $Phar, $Root);
            }
        }

        \vDesk::$Phar = false;

        //Delete temporary Phar archive.
        File::Delete($Phar->getPath());

        //Create client.
        $Client = new Client();
        foreach(self::Resolve() as $PackageToAdd) {
            $Client->AddPackage($PackageToAdd);
        }
        $Client->Create(\Client);

        Settings::$Local->Save();
        Settings::$Remote->Save();

        Log::Info(__METHOD__, "Installed Package '" . $Package::Name . "' v" . $Package::Version);

        return $Package;

    }

    /**
     * Uninstalls a specified Package and all dependent Packages.
     *
     * @param string|null $Package The Package to uninstall.
     *
     * @return string[] An array containing the Packages that have been uninstalled..
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to uninstall Packages.
     */
    public static function Uninstall(string $Package = null): array {
        if(!User::$Current->Permissions["UninstallPackage"]) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to uninstall Package without having permissions.");
            throw new UnauthorizedAccessException();
        }

        $Class = "\\vDesk\\Packages\\" . ($Package ?? Command::$Parameters["Package"]);
        /** @var \vDesk\Packages\Package $Package */
        $Package = new $Class();

        $Path        = Path::GetFullPath(\Server . Path::Separator . "..");
        $PackagePath = \Server . Path::Separator . Package::Lib . Path::Separator . "vDesk" . Path::Separator . "Packages" . Path::Separator;
        $Packages    = [];
        foreach(self::ResolveDependencies($Package) as $Dependency) {
            $Packages[] = $Dependency;
        }

        $Packages[] = $Package;
        foreach($Packages as $PackageToUninstall) {
            $PackageToUninstall::Uninstall($Path);
            File::Delete($PackagePath . $PackageToUninstall::Name . ".php");
        }

        //Uninstall specialized Packages.
        foreach(\vDesk\Modules::RunAll() as $Module) {
            if($Module instanceof IModule) {
                foreach($Packages as $PackageToUninstall) {
                    $Module::Uninstall($PackageToUninstall, $Path);
                }
            }
        }

        //Create client.
        $Client = new Client();
        foreach(self::Resolve() as $PackageToAdd) {
            $Client->AddPackage($PackageToAdd);
        }
        $Client->Create(\Client);

        return \array_map(static fn(Package $Package) => $Package::Name, $Packages);
    }

    /**
     * Resolves the dependencies of a specified Package.
     *
     * @param \vDesk\Packages\Package $Package The Package to resolve its dependencies of.
     *
     * @return \Generator A Generator that yields the dependent Packages of the specified Package.
     */
    public static function ResolveDependencies(Package $Package): \Generator {
        $Packages        = \array_reverse(self::Resolve()->ToArray());
        $GetDependencies = static function(Package $Dependency) use (&$GetDependencies, &$Packages): \Generator {
            foreach($Packages as $Package) {
                if(isset($Package::Dependencies[$Dependency::Name])) {
                    yield from $GetDependencies($Package);
                    yield $Package;
                }
            }
        };
        yield from $GetDependencies($Package);
    }

    /**
     * Resolves the installed Packages of vDesk.
     *
     * @return \vDesk\Struct\Collections\Collection A Collection containing all resolved Packages ordered by their dependencies.
     */
    public static function Resolve(): Collection {
        $Packages = [];
        /** @var \vDesk\IO\FileInfo $File */
        foreach(
            (new DirectoryInfo(
                \vDesk::$Phar
                    ? \Phar::running() . "/" . Package::Server . "/" . Package::Lib . "/vDesk/Packages"
                    : Path::GetFullPath(\Server . Path::Separator . Package::Lib . Path::Separator . "vDesk" . Path::Separator . "Packages")
            )
            )->IterateFiles("/")
            as
            $File
        ) {
            //Skip non Package files.
            if($File->Name === "Package" || $File->Name === "IModule" || $File->Name === "DependencyException") {
                continue;
            }
            $Class = "\\vDesk\\Packages\\" . $File->Name;
            /** @var \vDesk\Packages\Package $Package */
            $Packages[] = $Package = new $Class();
        }

        //Sort by dependencies and create client.
        $SortedPackages = new Collection();

        $Count = \count($Packages);

        //Packages without dependencies have the highest priority.
        foreach($Packages as $Index => $Package) {
            if(\count($Package::Dependencies) === 0) {
                $SortedPackages->Add($Package);
                \array_splice($Packages, $Index, 1);
            }
        }

        //Sort by dependencies.
        for($Resolved = 0; $SortedPackages->Count() < $Count; $Resolved++) {
            foreach($Packages as $Index => $Package) {
                if($SortedPackages->Any(fn(Package $SortedPackage): bool => $SortedPackage === $Package)) {
                    continue;
                }

                //Resolve dependencies.
                foreach($Package::Dependencies as $Dependency => $Version) {
                    if(!$SortedPackages->Any(fn(Package $SortedPackage): bool => $SortedPackage::Name === $Dependency)) {
                        $Resolved++;
                        if($Resolved === $Count) {
                            throw new \RuntimeException("Cannot resolve dependency Package '{$Dependency}' at version '{$Version}' of Package '" . $Package::Name . "'");
                        }
                        continue 2;
                    }
                }
                $SortedPackages->Add($Package);
                $Resolved = 0;
            }
        }
        return $SortedPackages;
    }
}