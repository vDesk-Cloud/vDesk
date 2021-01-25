<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Modules\Command;
use vDesk\Modules\Module;

/**
 * Description of Modules
 *
 * @author Kerry
 */
final class Modules extends Module {
    
    /**
     * Performs a handshake validating the specified set of versions for compatibility.
     * This Command can only be called from client scope.
     *
     * @return bool True if the client that wants to connect is compatible; otherwise, false.
     * @todo Use this method later for validating the versions of the modules for compatibility.
     */
    public static function Connect(): bool {
        return Command::$Parameters["Version"] === "0.1.2";
    }
    
    /**
     * Gets the Commands and Parameters of the currently installed Modules of vDesk.
     *
     * @return array The Commands and Parameters of the currently installed Modules of vDesk.
     */
    public static function GetCommands(): array {
        $Modules = [];
        /** @var \vDesk\Modules\Module $Module */
        foreach(\vDesk\Modules::RunAll() as $Module) {
            $Modules[] = $Module->ToDataView();
        }
        return $Modules;
    }
    
    /**
     * Gets the status information about every installed Module.
     *
     * @return array An array containing the status information of every installed Module.
     */
    public static function Status(): array {
        $Modules = [];
        /** @var \vDesk\Modules\Module $Module */
        foreach(\vDesk\Modules::RunAll() as $Module) {
            if($Module instanceof self){
                continue;
            }
            $Status = $Module::Status();
            if($Status !== null){
                $Modules[$Module->Name] = $Status;
            }
        }
        return $Modules;
    }
    
}
