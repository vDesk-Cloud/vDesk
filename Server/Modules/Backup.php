<?php
declare(strict_types=1);

namespace Modules;


class Backup {
    
    public static function Create(): \ZipArchive {
        
        //Dump database
        //Zip entire vDesk dir.
        //Serve download.
        
        return new \ZipArchive();
    }
    
}