<?php
declare(strict_types=1);

namespace vDesk\Pages;

use vDesk\IO\DirectoryInfo;
use vDesk\IO\File;
use vDesk\IO\Path;
use vDesk\Modules\Module;
use vDesk\Modules\UnknownModuleException;
use vDesk\Struct\Collections\Dictionary;

/**
 * Class Modules represents ...
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Modules extends \vDesk\Modules {
    
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
        
        //@todo Make false before running Patch.
        if(!\vDesk::$Phar && !static::Installed($Name)) {
            throw new UnknownModuleException("Module '{$Name}' doesn't exist!");
        }
        
        $Class = "Modules\\{$Name}";
        self::$Running->Add($Name, new $Class());
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
        
        /** @var \vDesk\IO\FileInfo $File */
        foreach(
            (new DirectoryInfo(
                \vDesk::$Phar
                    ? \Phar::running() . "/Server/Modules"
                    : Path::GetFullPath(\Server . Path::Separator . "Modules")
            )
            )->IterateFiles("/")
            as
            $Module
        ) {
            if(!self::$Running->ContainsKey($Module->Name)) {
                $Class = "Modules\\{$Module->Name}";
                self::$Running->Add($Module->Name, new $Class());
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
        return File::Exists(\Server . Path::Separator . "Modules" . Path::Separator . "{$Name}.php");
    }
    
}

new Modules();