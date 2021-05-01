<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Archive\Element;
use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
use vDesk\Environment\OS;
use vDesk\IO\FileInfo;
use vDesk\IO\Path;
use vDesk\Machines\IPackage;
use vDesk\Machines\Machine;
use vDesk\Modules\Command;
use vDesk\Modules\Module;
use vDesk\Packages\Package;
use vDesk\Packages\IModule;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Struct\Collections\Observable\Collection;
use vDesk\Struct\Guid;
use vDesk\Struct\InvalidOperationException;
use vDesk\Struct\Text;
use vDesk\Utils\Log;

/**
 * Machines Module.
 *
 * @package Modules
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Machines extends Module implements IModule {
    
    /**
     * Machines constructor.
     *
     * @param null|int                                             $ID
     * @param null|\vDesk\Struct\Collections\Observable\Collection $Commands
     */
    public function __construct(?int $ID = null, Collection $Commands = null) {
        parent::__construct($ID, $Commands);
        \vDesk::$Load[] = static fn(string $Class): string => Settings::$Local["Archive"]["Directory"]
                                                              . Path::Separator
                                                              . Expression::Select("File")
                                                                          ->From("Archive.Elements")
                                                                          ->Where(
                                                                              [
                                                                                  "Parent"    => Settings::$Local["Machines"]["Directory"],
                                                                                  "Name"      => \str_replace("vDesk\\Machines\\", "", $Class),
                                                                                  "Extension" => "php"
                                                                              ]
                                                                          )
                                                                          ->Limit(1)();
    }
    
    /**
     * Gets a collection of installed Machines.
     *
     * @return array The descriptions of every installed module.
     *
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to run Machines.
     */
    public static function Installed(): array {
        if(!\vDesk::$User->Permissions["RunMachine"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to get installed Machines without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Machines = [];
        foreach(
            Expression::Select("Name")
                      ->From("Archive.Elements")
                      ->Where(
                          [
                              "Parent"    => Settings::$Local["Machines"]["Directory"],
                              "Extension" => "php"
                          ]
                      )
            as $Machine
        ) {
            $Machines[] = $Machine["Name"];
        }
        return $Machines;
    }
    
    /**
     * Gets a collection of installed Machines.
     *
     * @return array The descriptions of every installed module.
     *
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to run Machines.
     */
    public static function Running(): array {
        if(!\vDesk::$User->Permissions["RunMachine"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to get running Machines without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Running = [];
        foreach(
            Expression::Select("*")
                      ->From("Machines.Running")
                      ->OrderBy(["TimeStamp"])
            as
            $Machine
        ) {
            $Machine["ID"]        = (int)$Machine["ID"];
            $Machine["Owner"]     = ["ID" => (int)$Machine["Owner"]];
            $Machine["TimeStamp"] = (int)$Machine["TimeStamp"];
            $Running[]            = $Machine;
        }
        return $Running;
    }
    
    /**
     * Starts a new Machine.
     *
     * @param null|string $Name The name of the Machine to start.
     *
     * @return \vDesk\Machines\Machine The started Machine.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to start the Machine.
     */
    public static function Start(string $Name = null): Machine {
        if(!\vDesk::$User->Permissions["RunMachine"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to start Machine without having permissions.");
            throw new UnauthorizedAccessException();
        }
        
        //Initialize Machine.
        $Name  ??= Command::$Parameters["Name"];
        $Class = "\\vDesk\\Machines\\{$Name}";
        /** @var \vDesk\Machines\Machine $Machine */
        $Machine = new $Class(
            null,
            \vDesk::$User,
            Guid::Create()
        );
        $Machine->Save();
    
        //Run Machine.
        switch(OS::Current) {
            case OS::NT:
                \pclose(
                    \popen(
                        "start /B php " . \Server . "\\vDesk.php -M=Machines -C=Run --Guid=\"\\\"{$Machine->Guid}\"\\\" --Ticket=\"{$Machine->Owner->Ticket}\"",
                        "r"
                    )
                );
                break;
            case OS::Linux:
                \exec("php " . \Server . "/vDesk.php -M=Machines -C=Run --Guid=\"\\\"{$Machine->Guid}\"\\\" --Ticket=\"{$Machine->Owner->Ticket}\" > /dev/null &");
                break;
        }
        
        //Get PID.
        $Machine->Fill();
        while($Machine->ID === 0) {
            \usleep(100000);
            $Machine->Fill();
        }
        
        return $Machine;
    }
    
    /**
     * Runs a specified Machine.
     *
     * @param null|string $Guid The Guid of the Machine to host.
     *
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to run Machines.
     */
    public static function Run(string $Guid = null): void {
        if(!\vDesk::$User->Permissions["RunMachine"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to run Machine without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Guid ??= Command::$Parameters["Guid"];
        $Name = Expression::Select("Name")
                          ->From("Machines.Running")
                          ->Where(["Guid" => $Guid])();
        
        $Class = "\\vDesk\\Machines\\{$Name}";
        /** @var \vDesk\Machines\Machine $Machine */
        $Machine = new $Class(
            \getmypid(),
            \vDesk::$User,
            $Guid,
            \time(),
            Machine::Running
        );
        $Machine->Save();
        $Machine->Start();
        
        $Pointer = \shmop_open($Machine->ID, "c", 0644, 1);
        \shmop_write($Pointer, Machine::Running, 0);
        
        while(true) {
            switch(\shmop_read($Pointer, 0, 1)) {
                case Machine::Running:
                    //Run Machine.
                    $Machine->Run();
                    break;
                case Machine::Suspended:
                    //Suspend Machine.
                    $Machine->Status = Machine::Suspended;
                    $Machine->Suspend();
                    $Machine->Save();
                    
                    //Idle until the status of the Machine has been changed.
                    while(\shmop_read($Pointer, 0, 1) === Machine::Suspended) {
                        \usleep(100000);
                    }
                    
                    //Resume Machine.
                    $Machine->Status = Machine::Running;
                    $Machine->Resume();
                    $Machine->Save();
                    break;
                case Machine::Stopped:
                    $Machine->Stop();
            }
        }
    }
    
    /**
     * Suspends a running Machine.
     *
     * @param null|string $Guid The guid of the Machine to suspend.
     *
     * @return bool True if the Machine has been successfully suspended.
     * @throws \vDesk\Struct\InvalidOperationException Thrown if the Machine doesn't exist.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to run Machines.
     */
    public static function Suspend(string $Guid = null): bool {
        if(!\vDesk::$User->Permissions["RunMachine"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to suspend Machine without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Guid ??= Command::$Parameters["Guid"];
        $ID   = (int)Expression::Select("ID")
                               ->From("Machines.Running")
                               ->Where(["Guid" => $Guid])();
        if($ID === 0) {
            throw new InvalidOperationException("Machine with with Guid {$Guid} doesn't exist!");
        }
        Expression::Update("Machines.Running")
                  ->Set(["Status" => Machine::Suspended])
                  ->Where(["Guid" => $Guid])();
        $Pointer = \shmop_open($ID, "w", 0644, 1);
        \shmop_write($Pointer, Machine::Suspended, 0);
        return true;
    }
    
    /**
     * Resumes a suspended Machine.
     *
     * @param null|string $Guid The guid of the Machine to resume.
     *
     * @return bool True if the Machine has been successfully resumed.
     * @throws \vDesk\Struct\InvalidOperationException Thrown if the Machine doesn't exist.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to run Machines.
     */
    public static function Resume(string $Guid = null): bool {
        if(!\vDesk::$User->Permissions["RunMachine"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to resume Machine without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Guid ??= Command::$Parameters["Guid"];
        $ID   = (int)Expression::Select("ID")
                               ->From("Machines.Running")
                               ->Where(["Guid" => $Guid])();
        if($ID === 0) {
            throw new InvalidOperationException("Machine with with Guid {$Guid} doesn't exist!");
        }
        Expression::Update("Machines.Running")
                  ->Set(["Status" => Machine::Running])
                  ->Where(["Guid" => $Guid])();
        $Pointer = \shmop_open($ID, "w", 0644, 1);
        \shmop_write($Pointer, Machine::Running, 0);
        return true;
    }
    
    /**
     * Stops a Machine.
     *
     * @param null|string $Guid The guid of the Machine to stop.
     *
     * @return bool
     * @throws \vDesk\Struct\InvalidOperationException Thrown if the Machine doesn't exist.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to run Machines.
     */
    public static function Stop(string $Guid = null): bool {
        if(!\vDesk::$User->Permissions["RunMachine"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to stop Machine without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Guid ??= Command::$Parameters["Guid"];
        $ID   = (int)Expression::Select("ID")
                               ->From("Machines.Running")
                               ->Where(["Guid" => $Guid])();
        if($ID === 0) {
            throw new InvalidOperationException("Machine with with Guid {$Guid} doesn't exist!");
        }
        Expression::Delete()
                  ->From("Machines.Running")
                  ->Where(["Guid" => $Guid])();
        $Pointer = \shmop_open($ID, "w", 0644, 1);
        \shmop_write($Pointer, Machine::Stopped, 0);
        return true;
    }
    
    /**
     * Terminates a Machine.
     *
     * @param null|string $Guid The guid of the Machine to terminate.
     *
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to run Machines.
     */
    public static function Terminate(string $Guid = null): void {
        if(!\vDesk::$User->Permissions["RunMachine"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to terminate Machine without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Guid ??= Command::$Parameters["Guid"];
        $ID   = (int)Expression::Select("ID")
                               ->From("Machines.Running")
                               ->Where(["Guid" => $Guid])();
        switch(OS::Current) {
            case OS::NT:
                `taskkill /F /PID {$ID}`;
                break;
            case OS::Linux:
                `kill -15 {$ID}`;
                break;
        }
        Expression::Delete()
                  ->From("Machines.Running")
                  ->Where(["ID" => $ID])
                  ->Execute();
    }
    
    /**
     * Reaps dead Machines.
     *
     * @return bool True if the Machine has been successfully resumed.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to run Machines.
     */
    public static function Reap(): bool {
        if(!\vDesk::$User->Permissions["RunMachine"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to reap Machines without having permissions.");
            throw new UnauthorizedAccessException();
        }
        foreach(
            Expression::Select("ID", "Guid")
                      ->From("Machines.Running")
            as
            $Machine
        ) {
            if(
                match (OS::Current) {
                    OS::NT => !\str_contains(`tasklist /fi "PID eq {$Machine["ID"]}" /fi "ImageName eq php.exe"` ?? "", $Machine["ID"] ?? ""),
                    OS::Linux => \str_contains(`kill -0 {$Machine["ID"]}` ?? "", "No such process")
                }
            ) {
                Expression::Delete()
                          ->From("Machines.Running")
                          ->Where(["Guid" => $Machine["Guid"]])
                          ->Execute();
                @\shmop_delete(\shmop_open((int)$Machine["ID"], "c", 0644, 1));
            }
        }
        
        return true;
    }
    
    /**
     * Installs the Machines of a specified Package.
     *
     * @param \vDesk\Packages\Package $Package The Package to install.
     * @param \Phar                   $Phar    The Phar archive of the Package.
     * @param string                  $Path    The installation path of the Package.
     */
    public static function Install(Package $Package, \Phar $Phar, string $Path): void {
        if($Package instanceof IPackage) {
            Settings::$Local["Log"]["Level"] = Log::Warn;
            foreach($Package::Machines as $Machine => $File) {
                
                //Check if the path starts with a separator.
                if(!Text::StartsWith($File, "/") && !Text::StartsWith($File, Path::Separator)) {
                    $File = Path::Separator . $File;
                }
                
                $FileInfo = new FileInfo(
                    $Path .
                    Path::Separator .
                    Package::Server .
                    Path::Separator .
                    Package::Lib .
                    Text::Replace($File, "/", Path::Separator)
                );
                
                \vDesk\Modules::Archive()::Upload(
                    new Element(Settings::$Local["Machines"]["Directory"]),
                    "{$FileInfo->Name}.{$FileInfo->Extension}",
                    $FileInfo
                );
            }
            Settings::$Local["Log"]["Level"] = Log::Debug;
            Log::Info(__METHOD__, "Successfully installed Machines of Package '" . $Package::Name . "' (v" . $Package::Version . ").");
        }
    }
    
    /**
     * Uninstalls the Machines of a specified Package.
     *
     * @param \vDesk\Packages\Package $Package The Package to uninstall.
     * @param string                  $Path    The installation path of the Package.
     */
    public static function Uninstall(Package $Package, string $Path): void {
        if($Package instanceof IPackage) {
            foreach(
                Expression::Select("ID")
                          ->From("Archive.Elements")
                          ->Where([
                              "Parent" => Settings::$Local["Machines"]["Directory"],
                              "Name"   => ["IN" => \array_map(fn($File): string => Path::GetFileName($File, false), \array_values($Package::Machines))]
                          ])
                as $Machine
            ) {
                \vDesk\Modules::Archive()::DeleteElements([(int)$Machine]);
            }
        }
    }
    
}