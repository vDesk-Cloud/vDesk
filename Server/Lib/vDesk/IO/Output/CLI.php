<?php
declare(strict_types=1);

namespace vDesk\IO\Output;

use vDesk\Modules\Command;
use vDesk\Data\IDataView;
use vDesk\IO\FileInfo;
use vDesk\IO\IStream;

/**
 * Interface providing functionality for writing data to a command line interface.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class CLI implements IProvider {

    /**
     * Sends data to the current output API.
     *
     * @param mixed $Data The data to send.
     */
    public static function Write(mixed $Data): void {

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