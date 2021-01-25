<?php
declare(strict_types=1);

namespace vDesk\IO\Output;

use vDesk\IO\IReadableStream;
use vDesk\Modules\Command;
use vDesk\Data\IDataView;
use vDesk\IO\FileInfo;
use vDesk\IO\FileStream;
use vDesk\IO\Stream;
use vDesk\Struct\StaticSingleton;

/**
 * Class CGI represents ...
 *
 * @package vDesk\Connection\Output
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
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
            \header("Content-Type: application/json");
            static::$Stream->Write(
                \json_encode([
                    "Status"  => true,
                    "Code"    => Command::Successful,
                    "Module"  => Command::$Module,
                    "Command" => Command::$Name,
                    "Data"    => $Data->ToDataView()
                ],
                    \JSON_THROW_ON_ERROR
                )
            );
            return;
        }
        
        //Check if a file has been passed.
        if($Data instanceof FileInfo) {
            \header("Content-type: " . ($Data->MimeType ?? "application/octet-stream"));
            \header("Content-Disposition: inline; filename={$Data->Name}.{$Data->Extension}");
            \header("Content-Length: " . $Data->Size);
            $FileStream = $Data->Open();
            while(!$FileStream->EndOfStream()) {
                static::$Stream->Write($FileStream->Read($FileStream::DefaultChunkSize), static::$Stream::DefaultChunkSize);
            }
            return;
        }
        
        //Check if a Stream has been passed.
        if($Data instanceof IReadableStream) {
            while(!$Data->EndOfStream()) {
                static::$Stream->Write($Data->Read($Data::DefaultChunkSize), static::$Stream::DefaultChunkSize);
            }
            $Data->Close();
            return;
        }
        
        //Check if a Generator has been passed.
        if($Data instanceof \Generator) {
            foreach($Data as $Bytes) {
                static::$Stream->Write($Bytes);
            }
            return;
        }
        \header("Content-Type: application/json");
        
        //Check if an Exception has been thrown.
        if($Data instanceof \Throwable) {
            static::$Stream->Write(
                \json_encode([
                    "Status"  => false,
                    "Code"    => $Data->getCode(),
                    "Module"  => Command::$Module,
                    "Command" => Command::$Name,
                    "Data"    => $Data->getMessage(),
                    "Stack"   => $Data->getTrace()
                ],
                    \JSON_THROW_ON_ERROR
                )
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
            ],
                \JSON_THROW_ON_ERROR
            )
        );
        
    }
}

new CGI();