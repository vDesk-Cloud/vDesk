<?php
declare(strict_types=1);

namespace vDesk;

use vDesk\DataProvider\Expression;
use vDesk\IO\DirectoryInfo;
use vDesk\IO\File;
use vDesk\IO\Path;
use vDesk\Modules\Module;
use vDesk\Modules\Proxy;
use vDesk\Modules\UnknownModuleException;
use vDesk\Struct\Collections\Dictionary;
use vDesk\Struct\StaticSingleton;
use vDesk\Struct\Text;

/**
 * Class providing functionality for working with installed Modules.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Modules extends StaticSingleton {
    
    /**
     * The currently running Modules of vDesk.
     *
     * @var \vDesk\Struct\Collections\Dictionary
     */
    public static Dictionary $Running;
    
    /**
     * Initializes the functionality of the StaticSingleton class.
     */
    public static function _construct(): void {
        self::$Running = new Dictionary();
        if(\vDesk::$Phar) {
            \vDesk::$Load[] = static fn(string $Class): string => \Phar::running() . "/Server/" . Text::Replace($Class, "\\", "/") . ".php";
        } else {
            \vDesk::$Load[] = static fn(string $Class): string => \Server . Path::Separator . Text::Replace($Class, "\\", Path::Separator) . ".php";
        }
    }
    
    /**
     * Runs the Module with the specified name and returns the instance of the Module.
     *
     * @param string $Module    The name of the Module to run.
     * @param mixed  $arguments This argument is omitted.
     *
     * @return \vDesk\Modules\Module The specified Module.
     */
    public static function __callStatic($Module, $arguments = null): Module {
        return self::Run($Module);
    }
    
    /**
     * Runs a specified Module.
     *
     * @param string     $Name The name of the Module to run.
     * @param \Phar|null $Phar The optional PHAR archive to load the Module from.
     *
     * @return \vDesk\Modules\Module The instance of the Module to run.
     * @throws \vDesk\Modules\UnknownModuleException Thrown if the Module to run doesn't exist.
     */
    public static function Run(string $Name, \Phar $Phar = null): Module {
        
        if(self::$Running->offsetExists($Name)) {
            return self::$Running[$Name];
        }
        
        $Class = "Modules\\" . $Name;
        
        //@todo Make false before running Patch.
        if(\vDesk::$Phar) {
            self::$Running->Add($Name, new $Class());
            return self::$Running[$Name];
        }
        
        $ResultSet = Expression::Select("ID", "Remote")
                               ->From("Modules.Modules")
                               ->Where(["Name" => $Name])
                               ->Execute();
        if($ResultSet->Count === 0) {
            throw new UnknownModuleException("Module '{$Name}' doesn't exist!");
        }
        
        $Module = $ResultSet->ToMap();
        if((bool)$Module["Remote"]) {
            self::$Running->Add($Name, new Proxy((int)$Module["ID"]));
        } else {
            self::$Running->Add($Name, new $Class((int)$Module["ID"]));
        }
        return self::$Running[$Name];
    }
    
    /**
     * Runs a Module and calls a Command on it.
     *
     * @param string $Module     The name of the Module to run.
     * @param string $Command    The name of the Command to call.
     * @param array  $Parameters The parameters to pass to the Module to call.
     *
     * @return mixed The result of the call.
     */
    public static function Call(string $Module, string $Command, ...$Parameters) {
        return self::Run($Module)::{$Command}(...$Parameters);
    }
    
    /**
     * Runs and returns all installed Modules of vDesk.
     *
     * @return \vDesk\Struct\Collections\Dictionary The current installed Modules of vDesk.
     */
    public static function RunAll(): Dictionary {
        
        if(\vDesk::$Phar) {
            /** @var \vDesk\IO\FileInfo $File */
            foreach((new DirectoryInfo(\Server . Path::Separator . "Modules"))->IterateFiles() as $File) {
                if(!self::$Running->ContainsKey($File->Name)) {
                    $Class = "Modules\\{$File->Name}";
                    self::$Running->Add($File->Name, new $Class());
                }
            }
            return self::$Running;
        }
        
        foreach(
            Expression::Select("*")
                      ->From("Modules.Modules")
                      ->Where(["Name" => ["NOT IN" => self::$Running->Keys]])
            as
            $Module
        ) {
            if((bool)$Module["Remote"]) {
                self::$Running->Add($Module["Name"], new Proxy((int)$Module["ID"]));
            } else {
                $Class = "Modules\\{$Module["Name"]}";
                self::$Running->Add($Module["Name"], new $Class((int)$Module["ID"]));
            }
        }
        return self::$Running;
    }
    
    /**
     * Determines whether a specified Module is installed.
     *
     * @param string $Name The name of the Module to check.
     *
     * @return bool True if the Module is installed; otherwise, false.
     */
    public static function Installed(string $Name): bool {
        if(!File::Exists(\Server . Path::Separator . "Modules" . Path::Separator . "{$Name}.php")) {
            return Expression::Select("ID")
                             ->From("Modules.Modules")
                             ->Where(["Name" => $Name])
                             ->Execute()
                    ->Count > 0;
        }
        return true;
    }
    
}

new Modules();