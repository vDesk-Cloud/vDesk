<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Client;
use vDesk\Configuration\Settings;
use vDesk\IO\File;
use vDesk\IO\FileInfo;
use vDesk\IO\HTTP\Client\Request;
use vDesk\IO\Path;
use vDesk\Modules\Command;
use vDesk\Modules\Module;
use vDesk\Packages\DependencyException;
use vDesk\Packages\Package;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Security\User;
use vDesk\Struct\InvalidOperationException;
use vDesk\Struct\Text;
use vDesk\Updates\IModule;
use vDesk\Updates\Update;
use vDesk\Utils\Log;

/**
 * Updates Module.
 *
 * @package vDesk\Updates
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Updates extends Module {

    /**
     * Queries the configured update servers for new Updates.
     *
     * @return array An array containing all available Updates.
     */
    public static function Search(): array {

        //Gather Packages.
        $Packages = [];
        $Updates  = [];
        foreach(\vDesk\Modules::Packages()::Resolve() as $Package) {
            $Packages[$Package::Name] = $Package::Version;
        }

        //Query Update servers.
        foreach(Settings::$Local["Updates"]["Server"] as $URL) {
            try {
                $Response = Request::Post(
                    $URL,
                    ["Packages" => \json_encode($Packages)],
                    ["Module" => "UpdateHost", "Command" => "Available"],
                    ["Content-Type" => "application/x-www-form-urlencoded", "Accept-Encoding" => "identity"] + Request::DefaultHeaders
                )
                                   ->Send();
                if($Response->Code !== 200) {
                    continue;
                }
                foreach($Response->Message["Data"] as $Update) {
                    $Updates[] = ["Source" => $URL] + $Update;
                }
            } catch(\Throwable $Exception) {
                Log::Error(__METHOD__, $Exception->getMessage());
            }
        }

        //Check if the UpdateHost Package has been installed (soft dependency).
        if(\vDesk\Modules::Installed("UpdateHost")) {
            foreach(\vDesk\Modules::UpdateHost()::Available($Packages) as $Update) {
                $Updates[] = ["Source" => Update::Hosted] + $Update;
            }
        }

        return $Updates;
    }

    /**
     * Creates an Update.
     *
     * @param null|string $Update      The name of the Update to create.
     * @param string|null $Path        The output path of the Update to create.
     * @param null|int    $Compression Optional compression of the Update to create.
     *
     * @return \vDesk\Updates\Update An instance of the Update representing the created Update.
     *
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to install Updates.
     */
    public static function Create(string $Update = null, string $Path = null, int $Compression = null): Update {
        if(!User::$Current->Permissions["InstallUpdate"]) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to create Update without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Update   ??= Command::$Parameters["Update"];
        $Packages = Package::Server . "/" . Package::Lib . "/vDesk/Packages";
        $Updates  = Package::Server . "/" . Package::Lib . "/vDesk/Updates";

        $UpdateClass = "\\vDesk\\Updates\\{$Update}";
        /** @var \vDesk\Updates\Update $Update */
        $Update       = new $UpdateClass();
        $PackageClass = $UpdateClass::Package;
        /** @var \vDesk\Packages\Package $Package */
        $Package = new $PackageClass();

        //Create Update.phar
        $Phar = (new \Phar(($Path ?? Command::$Parameters["Path"] ?? \Server) . Path::Separator . $Package::Name . "[" . $Package::Version . "].phar"));
        $Phar->setSignatureAlgorithm(\Phar::SHA256);
        $Phar->startBuffering();
        $Name = $Package::Name;
        $Phar->setStub(
            <<<STUB
<?php
    Phar::mapPhar(__FILE__);
    include "phar://" . __FILE__ . "/$Updates/{$Name}.php";
    include "phar://" . __FILE__ . "/$Packages/{$Name}.php";
    return [new {$UpdateClass}(), new {$PackageClass}()];
    __HALT_COMPILER();
STUB
        );

        $Update::Compose($Phar);

        //Bundle Package manifest if not already happened by the Update itself.
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

        //Bundle Update manifest if not already happened by the Update itself.
        if(!isset($Phar[$Updates])) {
            $Phar->addEmptyDir($Updates);
            $Phar->addFile(
                \Server
                . Path::Separator
                . Package::Lib
                . Path::Separator
                . "vDesk"
                . Path::Separator
                . "Updates"
                . Path::Separator
                . $Package::Name . ".php",
                "{$Updates}/" . $Package::Name . ".php"
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

        return $Update;
    }

    /**
     * Downloads and provides an Update from a remote host or local file.
     *
     * @param null|string $Source The source of the Update to install.
     * @param null|string $Hash   The hash of the Update to install.
     *
     * @return \vDesk\IO\FileInfo The downloaded Update file.
     * @throws \vDesk\Struct\InvalidOperationException Thrown if the Update can't be downloaded.
     */
    public static function Download(string $Source = null, string $Hash = null): FileInfo {
        $Source ??= Command::$Parameters["Source"];

        //Check if the requested Update is locally hosted.
        if($Source === Update::Hosted) {
            return \vDesk\Modules::UpdateHost()::Download($Hash);
        }

        //Download update from remote host.
        $Response = Request::Post(
            $Source,
            ["Hash" => \json_encode($Hash ?? Command::$Parameters["Hash"])],
            ["Module" => "UpdateHost", "Command" => "Download", "Hash" => $Hash ?? Command::$Parameters["Hash"]],
            ["Content-Type" => "application/x-www-form-urlencoded"] + Request::DefaultHeaders
        )->Send();

        if($Response->Code !== 200 || $Response->Headers["Content-Type"] !== Update::MimeType) {
            throw new InvalidOperationException($Response->Message);
        }

        return $Response->Message;
    }

    /**
     * Installs an Update from a remote host or local file.
     *
     * @param null|string $Source The source of the Update to install.
     * @param null|string $Hash   The hash of the Update to install.
     *
     * @return \vDesk\Updates\Update The installed Update.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to install Updates.
     */
    public static function Install(string $Source = null, string $Hash = null): Update {
        if(!User::$Current->Permissions["InstallUpdate"]) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to install Update without having permissions.");
            throw new UnauthorizedAccessException();
        }
        return self::InstallUpdate(self::Download($Source, $Hash));
    }

    /**
     * Installs an Update from a remote host or local file.
     *
     * @param null|\vDesk\IO\FileInfo $Update The Phar archive of the Update to install.
     *
     * @return \vDesk\Updates\Update The installed Update.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to install Updates.
     */
    public static function Deploy(FileInfo $Update = null): Update {
        if(!User::$Current->Permissions["InstallUpdate"]) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to install Update without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Update ??= Command::$Parameters["Update"];

        //Save Update file.
        $Path       = \sys_get_temp_dir() . Path::Separator . "{$Update->Name}.phar";
        $TargetFile = File::Create($Path);
        $TempFile   = $Update->Open();
        while(!$TempFile->EndOfStream()) {
            $TargetFile->Write($TempFile->Read());
        }
        $TargetFile->Close();
        $TempFile->Close();
        //Delete temp file.
        $Update->Delete();

        return self::InstallUpdate(new FileInfo($Path));
    }

    /**
     * Installs a specified Update.
     *
     * @param \vDesk\IO\FileInfo $Update The Phar archive of the Update to install.
     *
     * @return \vDesk\Updates\Update The installed Update.
     * @throws \vDesk\Packages\DependencyException
     */
    private static function InstallUpdate(FileInfo $Update): Update {
        $Root = Path::GetFullPath(\Server . Path::Separator . "..");
        $Phar = new \Phar($Update->Path);

        //Wire autoload to PHAR archive.
        \vDesk::$Load[] = static fn(string $Class): string => "phar://{$Phar->getPath()}/Server/Lib/" . Text::Replace($Class, "\\", "/") . ".php";
        \vDesk::$Load[] = static fn(string $Class): string => "phar://{$Phar->getPath()}/Server/" . Text::Replace($Class, "\\", "/") . ".php";

        //Load Update.
        /** @var \vDesk\Updates\Update $Update */
        /** @var \vDesk\Packages\Package $Package */
        [$Update, $Package] = include $Phar->getPath();

        //Validate dependencies.
        $Packages = \vDesk\Modules::Packages()::Resolve();
        foreach($Package::Dependencies as $Dependency => $Version) {
            $DependencyPackage = $Packages->Find(fn(Package $Installed): bool => $Installed::Name === $Dependency);
            if($DependencyPackage === null) {
                throw new DependencyException("Missing dependency package '{$Dependency}' in version '{$Version}'!");
            }
            if(\version_compare($DependencyPackage::Version, $Version) < 0) {
                throw new DependencyException("Requiring dependency package '{$Dependency}' in version '{$Version}', but installed '" . $DependencyPackage::Version . "'!");
            }
        }

        //Install Update.
        \vDesk::$Phar = true;
        $Update::Install($Phar, $Root);

        //Deploy Update's Package manifest file if not already happened by the Update itself.
        $Manifest = \Server
                    . Path::Separator
                    . Package::Lib
                    . Path::Separator
                    . "vDesk"
                    . Path::Separator
                    . "Packages"
                    . Path::Separator
                    . $Package::Name . ".php";
        if(File::Exists($Manifest)) {
            File::Delete($Manifest);
        }
        File::Copy(
            "phar://{$Phar->getPath()}/" . Package::Server . "/" . Package::Lib . "/vDesk/Packages/" . $Package::Name . ".php",
            $Manifest
        );

        //Install specialized Updates.
        foreach(\vDesk\Modules::RunAll() as $Module) {
            if($Module instanceof IModule) {
                $Module::Update($Update, $Phar, $Root);
            }
        }

        \vDesk::$Phar = false;

        //Delete temporary Phar archive.
        File::Delete($Phar->getPath());

        //Re-create client.
        $Client = new Client();
        foreach(\vDesk\Modules::Packages()::Resolve() as $PackageToAdd) {
            $Client->AddPackage($PackageToAdd);
        }
        $Client->Create(\Client);

        Settings::$Local->Save();
        Settings::$Remote->Save();

        Log::Info(__METHOD__, "Installed Update '" . $Package::Name . "' v" . $Package::Version);

        return $Update;
    }

}