<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Client;
use vDesk\Configuration\Settings\Local\Settings;
use vDesk\Modules\Command;
use vDesk\IO\DirectoryInfo;
use vDesk\IO\File;
use vDesk\IO\FileInfo;
use vDesk\IO\Path;
use vDesk\Modules\Module;
use vDesk\Package;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Struct\Collections\Collection;
use vDesk\Struct\Text;
use vDesk\Utils\Log;

/**
 * Class Packages represents ...
 *
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
final class Packages extends Module {
    
    /**
     * The Phar loader stub of the install setup.
     */
    public const Stub = <<<STUB
<?php
    Phar::mapPhar("Setup.phar");
    include "phar://Setup.phar/Setup.php";
    __HALT_COMPILER();
STUB;
    
    /**
     * Crates a new setup.
     *
     * @param string|null $Path        The output path of the setup to create.
     * @param array|null  $Exclude     Optional array of Packages to exclude from the setup.
     * @param int         $Compression Optional compression of the setup to create.
     *
     * @return array An array containing the composed Packages of the created setup.
     *
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to install Packages.
     */
    public static function CreateSetup(string $Path = null, array $Exclude = null, int $Compression = \Phar::NONE): array {
        
        $Exclude ??= Command::$Parameters["Exclude"] ?? [];
        if(!\vDesk::$User->Permissions["InstallPackage"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to view installed Packages without having permissions.");
            throw new UnauthorizedAccessException();
        }
        
        //Create Setup.phar
        $Phar = (new \Phar(($Path ?? Command::$Parameters["Path"] ?? \Server) . Path::Separator . "Setup.phar"));
        $Phar->setStub(static::Stub);
        
        //Basic directory structure.
        $Phar->addEmptyDir("/" . Package::Client . "/" . Package::Design);
        $Phar->addEmptyDir("/" . Package::Client . "/" . Package::Modules);
        $Phar->addEmptyDir("/" . Package::Client . "/" . Package::Lib);
        $Phar->addEmptyDir("/" . Package::Server . "/" . Package::Modules);
        $Phar->addEmptyDir("/" . Package::Server . "/" . Package::Lib);
        $Phar->addFile(\Server . Path::Separator . Package::Lib . Path::Separator . "vDesk" . Path::Separator . "Setup.php", "/Setup.php");
        
        $Packages = [];
        //Bundle files.
        /** @var \vDesk\Package $Package */
        foreach(static::ResolvePackages() as $Package) {
            if(\in_array($Package::Name, $Exclude)) {
                continue;
            }
            $Package::Compose($Phar);
            $Packages[$Package::Name] = $Package::Version;
        }
        foreach($Exclude as $Package){
            File::Delete("phar://{$Phar->getPath()}/Server/Lib/vDesk/Packages/{$Package}.php");
        }
        return $Packages;
    }
    
    /**
     * Installs the setup and all bundled Packages.
     */
    public static function InstallSetup(): void {
        $Phar = new \Phar(\Phar::running(false));
        $Root = Text::Substring($Phar->getPath(), 0, Text::LastIndexOf($Phar->getPath(), "/"))->Replace("/", Path::Separator);
        
        Log::Info(__METHOD__, "Installing setup");
        
        $Packages = static::ResolvePackages();
        
        //Call pre-install hooks.
        Log::Info(__METHOD__, "Gathering information.");
        foreach($Packages as $Package) {
            $Package::PreInstall($Phar, (string)$Root);
        }
        
        //Create client.
        $Client = new Client();
        
        //Install Packages.
        Log::Info(__METHOD__, "Installing Packages.");
        foreach($Packages as $Package) {
            $Package::Install($Phar, (string)$Root);
            
            //Add client files.
            $Client->AddPackage($Package);
            
            Log::Info(__METHOD__, "Successfully installed Package '" . $Package::Name . "' (v" . $Package::Version . ").");
        }
        
        $Client->Create($Root . Path::Separator . Package::Client);
        
        //Call post-install hooks.
        Log::Info(__METHOD__, "Cleaning up installation.");
        foreach($Packages as $Package) {
            $Package::PostInstall($Phar, (string)$Root);
        }
        
        Log::Info(__METHOD__, "Delegating Packages.");
        foreach(\vDesk\Modules::$Running as $Module) {
            if($Module instanceof Package\IModule) {
                foreach($Packages as $Package) {
                    $Module::Install($Package, $Phar, (string)$Root);
                }
            }
        }
        
        Log::Info(__METHOD__, "Successfully installed setup.");
        
    }
    
    /**
     * Gets the description of every installed module.
     *
     * @return array The descriptions of every installed module.
     *
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to install Packages.
     */
    public static function GetPackages(): array {
        if(!\vDesk::$User->Permissions["InstallPackage"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to view installed Packages without having permissions.");
            throw new UnauthorizedAccessException();
        }
        return static::ResolvePackages()
                     ->Map(static fn(Package $Package): array => $Package->ToDataView())
                     ->ToArray();
    }
    
    /**
     * Installs a setup.
     *
     * @param string      $Package The name of the Package to create.
     * @param string|null $Path    The output path of the Package to create.
     *
     * @return \vDesk\Package An instance of the Package representing the created Package.
     *
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to install Packages.
     */
    public static function CreatePackage(string $Package = null, string $Path = null): Package {
        if(!\vDesk::$User->Permissions["InstallPackage"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to view installed Packages without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Package  ??= Command::$Parameters["Package"];
        $Packages = Package::Server . "/" . Package::Lib . "/vDesk/Packages";
        
        //Create Package.phar
        $Phar = (new \Phar(($Path ?? Command::$Parameters["Path"] ?? \Server) . Path::Separator . "{$Package}.phar"));
        
        $Class = "\\vDesk\\Packages\\{$Package}";
        $Stub  = <<<STUB
<?php
    Phar::mapPhar("{$Package}.phar");
    include "phar://{$Package}.phar/$Packages/{$Package}.php";
    return new {$Class}();
    __HALT_COMPILER();
STUB;
        $Phar->setStub($Stub);
        
        /** @var \vDesk\Package $Package */
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
        
        return $Package;
        
    }
    
    /**
     * Installs a specified Package.
     *
     * @param \vDesk\IO\FileInfo|null $Package The Phar archive of the Package to install.
     *
     * @return \vDesk\Package The installed Package.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to install Packages.
     * @throws \vDesk\Package\DependencyException Thrown if a dependency of the Package cannot be satisfied.
     */
    public static function InstallPackage(FileInfo $Package = null): Package {
        if(!\vDesk::$User->Permissions["InstallPackage"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to install Package without having permissions.");
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
        /** @var \vDesk\Package $Package */
        $Package = include $Phar->getPath();
        
        //Validate dependencies.
        $Packages = static::ResolvePackages();
        foreach($Package::Dependencies as $Dependency => $Version) {
            $DependencyPackage = $Packages->Find(fn(Package $Installed): bool => $Installed::Name === $Dependency);
            if($DependencyPackage === null) {
                throw new Package\DependencyException("Missing dependency package '{$Dependency}' in version '{$Version}'!");
            }
            if(\version_compare($DependencyPackage::Version, $Version) < 0) {
                throw new Package\DependencyException("Requiring dependency package '{$Dependency}' in version '{$Version}', but installed '" . $DependencyPackage::Version . "'!");
            }
        }
    
        //Install Package.
        \vDesk::$Phar   = true;
        $Package::PreInstall($Phar, $Root);
        $Package::Install($Phar, $Root);
        $Package::PostInstall($Phar, $Root);
        
        File::Copy(
            "phar://{$Phar->getPath()}/" . Package::Server . "/" . Package::Lib . "/vDesk/Packages/" . $Package::Name . ".php",
            \Server
            . Path::Separator
            . Package::Lib
            . Path::Separator
            . "vDesk"
            . Path::Separator
            . "Packages"
            . Path::Separator
            . $Package::Name . ".php"
        );
        
        \vDesk::$Phar = true;
        
        //Install specialized Packages.
        foreach(\vDesk\Modules::RunAll() as $Module) {
            if($Module instanceof Package\IModule) {
                $Module::Install($Package, $Phar, $Root);
            }
        }
        
        \vDesk::$Phar = false;
        
        //Delete temporary Phar archive.
        File::Delete($Phar->getPath());
        
        //Create client.
        $Client = new Client();
        foreach(static::ResolvePackages() as $PackageToAdd) {
            $Client->AddPackage($PackageToAdd);
        }
        $Client->Create(\Client);
        
        \vDesk\Configuration\Settings::$Local->Save();
        \vDesk\Configuration\Settings::$Remote->Save();
        
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
    public static function UninstallPackage(string $Package = null): array {
        if(!\vDesk::$User->Permissions["UninstallPackage"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to uninstall Package without having permissions.");
            throw new UnauthorizedAccessException();
        }
        
        $Class = "\\vDesk\\Packages\\" . ($Package ?? Command::$Parameters["Package"]);
        /** @var \vDesk\Package $Package */
        $Package = new $Class();
        
        $Path        = Path::GetFullPath(\Server . Path::Separator . "..");
        $PackagePath = \Server . Path::Separator . Package::Lib . Path::Separator . "vDesk" . Path::Separator . "Packages" . Path::Separator;
        $Packages    = [];
        foreach(self::ResolveDependencies($Package) as $Dependency) {
            $Packages[] = $Dependency;
        }
        
        $Packages[] = $Package;
        foreach($Packages as $PackageToUninstall) {
            $PackageToUninstall->Uninstall($Path);
            File::Delete($PackagePath . $PackageToUninstall::Name . ".php");
        }
        
        //Uninstall specialized Packages.
        foreach(\vDesk\Modules::RunAll() as $Module) {
            if($Module instanceof Package\IModule) {
                foreach($Packages as $PackageToUninstall) {
                    $Module::Uninstall($PackageToUninstall, $Path);
                }
            }
        }
        
        //Create client.
        $Client = new Client();
        foreach(static::ResolvePackages() as $PackageToAdd) {
            $Client->AddPackage($PackageToAdd);
        }
        $Client->Create(\Client);
        
        return \array_map(fn(Package $Package) => $Package::Name, $Packages);
    }
    
    /**
     * Resolves the dependencies of a specified Package.
     *
     * @param \vDesk\Package $Package The Package to resolve its dependencies of.
     *
     * @return \Generator A Generator that yields the dependent Packages of the specified Package.
     */
    public static function ResolveDependencies(Package $Package): \Generator {
        $Packages        = \array_reverse(self::ResolvePackages()->ToArray());
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
    public static function ResolvePackages(): Collection {
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
            $Class = "\\vDesk\\Packages\\" . $File->Name;
            /** @var \vDesk\Package $Package */
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