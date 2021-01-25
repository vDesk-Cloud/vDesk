<?php
declare(strict_types=1);

namespace vDesk\Tasks;


use vDesk\Modules;
use vDesk\Utils\Log;

class Reaper extends Task {
    
    public const Seconds = 30;
    
    public const Minutes = 0;
    
    public function Run(): \Generator {
        Log::Info(__METHOD__, "Let's reap dead Machines!");
        yield;
        
        Log::Info(__METHOD__, "Darf ich dich bosizionierne?");
        yield;
        //Modules::Machines()::Reap();
        //yield;
        Log::Info(__METHOD__, "Seggsschreibne?");
        yield;
    }
}