<?php
declare(strict_types=1);

namespace vDesk\IO\Output;

use vDesk\Data\IDataView;
use vDesk\Events\EventProvider;
use vDesk\IO\FileInfo;
use vDesk\IO\IStream;
use vDesk\Modules\Command;


/**
 * Class CLI
 *
 * @package vDesk\Pages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Pages implements IProvider {
    
    /**
     * Sends data to the current output API.
     *
     * @param mixed $Data The data to send.
     */
    public static function Write($Data): void {
        
        //Check if the passed data implements the IDataView interface.
        if($Data instanceof IDataView) {
            \fwrite(\STDOUT,
                \json_encode([
                    "Status"  => true,
                    "Code"    => Command::Successful,
                    "Module"  => Command::$Module,
                    "Command" => Command::$Name,
                    "Data"    => $Data->ToDataView()
                ])
            );
            return;
        }
        
        //Check if a file has been passed.
        if($Data instanceof FileInfo) {
            $FileStream = $Data->Open();
            while(!$FileStream->EndOfStream()) {
                \fwrite(\STDOUT, $FileStream->Read($FileStream::DefaultChunkSize), $FileStream::DefaultChunkSize);
            }
            $Data->Close();
            return;
        }
        
        //Check if an EventProvider has been passed.
        if($Data instanceof EventProvider) {
            // Flush events.
            foreach($Data->FetchEvents() as $Event => $EventData) {
                \fwrite(\STDOUT, "event: {$Event}\n");
                \fwrite(\STDOUT, "data: {$EventData}\n\n");
            }
            return;
        }
        
        //Check if a Stream has been passed.
        if($Data instanceof IStream) {
            while($Data->CanSeek()) {
                \fwrite(\STDOUT, $Data->Read($Data::DefaultChunkSize), $Data::DefaultChunkSize);
            }
            $Data->Close();
            return;
        }
        
        //Check if an Exception has been thrown.
        if($Data instanceof \Throwable) {
            \fwrite(\STDOUT,
                \json_encode([
                    "Status"     => false,
                    "Code"       => $Data->getCode(),
                    "Module"     => Command::$Module,
                    "Command"    => Command::$Name,
                    "Data"       => $Data->getMessage(),
                    "StackTrace" => $Data->getTrace() //@todo Remove this line when reaching version 1.0
                ])
            );
            return;
        }
        
        //Create a default response.
        \fwrite(\STDOUT,
            \json_encode([
                "Status"  => true,
                "Code"    => Command::Successful,
                "Module"  => Command::$Module,
                "Command" => Command::$Name,
                "Data"    => $Data
            ])
        );
        
    }
}