<?php
declare(strict_types=1);

namespace vDesk\IO\Output;

use vDesk\Modules\Command;
use vDesk\Data\IDataView;
use vDesk\Events\EventProvider;
use vDesk\IO\FileInfo;
use vDesk\IO\FileStream;
use vDesk\IO\IStream;
use vDesk\IO\Stream;
use vDesk\Struct\StaticSingleton;

/**
 * Class CGI represents ...
 *
 * @package vDesk\Connection\Output
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class CGI extends StaticSingleton implements IProvider {

    /**
     * The output-stream.
     *
     * @var null|\vDesk\IO\FileStream
     */
    protected static ?FileStream $Stream = null;

    /**
     * Initializes a new instance of the CGI class.
     */
    public static function _construct() {
        \header('Access-Control-Allow-Origin: *');
        \header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        static::$Stream = new FileStream("php://output", Stream\Mode::Read | Stream\Mode::Binary);
    }

    /**
     * Sends data to the current output API.
     *
     * @param mixed $Data The data to send.
     */
    public static function Write($Data): void {
    
        //Check if the passed data implements the IDataView interface.
        if($Data instanceof IDataView) {
            static::$Stream->Write(
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
            \header("Content-type: {$Data->MimeType}");
            \header("Content-Disposition: inline; filename={$Data->Name}.{$Data->Extension}");
            \header("Content-Length: " . $Data->Size);
            $FileStream = $Data->Open();
            while(!$FileStream->EndOfStream()) {
                static::$Stream->Write($FileStream->Read(), FileStream::DefaultChunkSize);
            }
            return;
        }
        
        //Check if an EventProvider has been passed.
        if($Data instanceof EventProvider) {
            if(\ob_get_level() > 0) {
                \ob_end_clean();
            }

            \header("Cache-Control: no-cache");
            \header("Content-Type: text/event-stream\n\n");
            // Flush events.
            foreach($Data->FetchEvents() as $Event => $EventData) {
                static::$Stream->Write("event: {$Event}\n");
                static::$Stream->Write("data: {$EventData}\n\n");
                \flush();
            }
            return;
        }
        
        //Check if a Stream has been passed.
        if($Data instanceof IStream) {
            while(!$Data->EndOfStream()) {
                static::$Stream->Write($Data->Read(), $Data::DefaultChunkSize);
            }
            $Data->Close();
            return;
        }
        
        //Check if an Exception has been thrown.
        if($Data instanceof \Throwable) {
            static::$Stream->Write(
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
        static::$Stream->Write(
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
new CGI();