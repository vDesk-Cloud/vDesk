<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Client;
use vDesk\IO\File;
use vDesk\IO\Path;
use vDesk\Modules\Command;
use vDesk\Modules\Module;
use vDesk\Packages;
use vDesk\Packages\Package;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Struct\Text;
use vDesk\Utils\Log;

/**
 * Setup Module class.
 *
 * @package Modules
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Setup extends Module {
    
    /**
     * The Phar loader stub of the install setup.
     */
    public const Stub = <<<STUB
<?php
    if(\PHP_VERSION_ID < 80000) {
        echo "vDesk requires at least PHP version 8.0";
        exit;
    }
    if(\PHP_SAPI !== "cli") {
        echo "This setup can only be run from the cli!";
        exit;
    }

    Phar::mapPhar(__FILE__);
    
    const Server = __DIR__ . \DIRECTORY_SEPARATOR . "Server";
    const Client = __DIR__ . \DIRECTORY_SEPARATOR . "Client";
    
    //Run vDesk in Phar mode.
    include "phar://" . __FILE__ . "/Server/Lib/vDesk.php";
    \\vDesk::Start(true);
    
    //Run setup.
    \\vDesk\Modules::Setup()::Install();

    __HALT_COMPILER();
STUB;
    
    /**
     * Creates a new setup.
     *
     * @param string|null $Path        The output path of the setup to create.
     * @param array|null  $Exclude     Optional array of Packages to exclude from the setup.
     * @param null|int    $Compression Optional compression of the setup to create.
     *
     * @return array An array containing the composed Packages of the created setup.
     *
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to install Packages.
     */
    public static function Create(string $Path = null, array $Exclude = null, int $Compression = null): array {
        $Exclude ??= Command::$Parameters["Exclude"] ?? [];
        if(!\vDesk::$User->Permissions["InstallPackage"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to create setup without having permissions.");
            throw new UnauthorizedAccessException();
        }
        
        //Create Setup.phar
        $Phar = (new \Phar(($Path ?? Command::$Parameters["Path"] ?? \Server) . Path::Separator . "Setup.phar"));
        $Phar->startBuffering();
        $Phar->setStub(self::Stub);
        
        //Basic directory structure.
        $Phar->addEmptyDir("/" . Package::Client . "/" . Package::Design);
        $Phar->addEmptyDir("/" . Package::Client . "/" . Package::Modules);
        $Phar->addEmptyDir("/" . Package::Client . "/" . Package::Lib);
        $Phar->addEmptyDir("/" . Package::Server . "/" . Package::Modules);
        $Phar->addEmptyDir("/" . Package::Server . "/" . Package::Lib);
        
        $Packages = [];
        //Bundle files.
        /** @var \vDesk\Packages\Package $Package */
        foreach(\vDesk\Modules::Packages()::Resolve() as $Package) {
            if(\in_array($Package::Name, $Exclude)) {
                continue;
            }
            $Package::Compose($Phar);
            $Packages[$Package::Name] = $Package::Version;
        }
        foreach($Exclude as $Package) {
            File::Delete("phar://{$Phar->getPath()}/Server/Lib/vDesk/Packages/{$Package}.php");
        }
        
        if(($Compression ??= Command::$Parameters["Compression"] ?? \Phar::NONE) !== \Phar::NONE) {
            //Yay, this never gets executed but is necessary to prevent PHP from crying about "phar exists and must be unlinked prior to conversion"...
            if($Compression === \Phar::GZ && File::Exists($Phar->getPath() . ".gz")){
                File::Delete($Phar->getPath() . ".gz");
            }else if($Compression === \Phar::BZ2 && File::Exists($Phar->getPath() . ".bz")){
                File::Delete($Phar->getPath() . ".bz");
            }
            $Phar->compress($Compression)->setStub($Phar->getStub());
        }
        $Phar->stopBuffering();
        
        return $Packages;
    }
    
    /**
     * Installs the setup and all bundled Packages.
     */
    public static function Install(): void {
        $Phar = new \Phar(\Phar::running(false));
        $Root = Text::Substring($Phar->getPath(), 0, Text::LastIndexOf($Phar->getPath(), "/"))->Replace("/", Path::Separator);
        
        Log::Info(__METHOD__, "Installing setup");
        
        $Packages = \vDesk\Modules::Packages()::Resolve();
        
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
            if($Module instanceof Packages\IModule) {
                foreach($Packages as $Package) {
                    $Module::Install($Package, $Phar, (string)$Root);
                }
            }
        }
        
        Log::Info(__METHOD__, "Successfully installed setup.");
        
    }
}