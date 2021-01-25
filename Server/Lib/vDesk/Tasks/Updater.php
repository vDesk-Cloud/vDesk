<?php
declare(strict_types=1);

namespace vDesk\Tasks;


use vDesk\Utils\Log;

class Updater extends Task {
    
    public const Seconds = 15;
    
    public const Minutes = 0;
    
    public function Run(): \Generator {
        Log::Info(__METHOD__, "Checking for updates");
        yield;
        
    }
}