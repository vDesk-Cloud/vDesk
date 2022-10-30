<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\Data\IDataView;
use vDesk\Modules\Command;

/**
 * Provides abstract access to the current output stream.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Output {

    /**
     * Writes data to the output-stream.
     *
     * @param mixed $Data The data to write.
     */
    public static function Write(mixed $Data): void {
        if(\PHP_SAPI === "cli") {
            self::CLI($Data);
        } else {
            self::CGI($Data);
        }
    }

    /**
     * Writes data to the command line interface.
     *
     * @param mixed $Data The data to send.
     *
     * @throws \JsonException
     */
    public static function CLI(mixed $Data): void {
        //Check if the passed data implements the IDataView interface.
        if($Data instanceof IDataView) {
            \fwrite(
                \STDOUT,
                \json_encode(
                    [
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
            $FileStream = $Data->Open();
            while(!$FileStream->EndOfStream()) {
                \fwrite(\STDOUT, $FileStream->Read());
            }
            return;
        }

        //Check if a Stream has been passed.
        if($Data instanceof IReadableStream) {
            while(!$Data->EndOfStream()) {
                \fwrite(\STDOUT, $Data->Read());
            }
            return;
        }

        //Check if a Generator has been passed.
        if($Data instanceof \Generator) {
            foreach($Data as $Bytes) {
                \fwrite(\STDOUT, $Bytes);
            }
            return;
        }

        //Check if an Exception has been thrown.
        if($Data instanceof \Throwable) {
            \fwrite(
                \STDOUT,
                \json_encode(
                    [
                        "Status"     => false,
                        "Code"       => $Data->getCode(),
                        "Module"     => Command::$Module,
                        "Command"    => Command::$Name,
                        "Data"       => $Data->getMessage(),
                        "StackTrace" => $Data->getTrace() //@todo Remove this line when reaching version 1.0
                    ],
                    \JSON_THROW_ON_ERROR
                )
            );
            return;
        }

        //Create a default response.
        \fwrite(
            \STDOUT,
            \json_encode(
                [
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

    /**
     * Sends data to the common gateway interface.
     *
     * @param mixed $Data The data to send.
     *
     * @throws \JsonException
     */
    public static function CGI(mixed $Data): void {
        \header('Access-Control-Allow-Origin: *');
        \header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        $Stream = new FileStream("php://output", Stream\Mode::Read | Stream\Mode::Binary);
        //Check if the passed data implements the IDataView interface.
        if($Data instanceof IDataView) {
            \header("Content-Type: application/json");
            $Stream->Write(
                \json_encode(
                    [
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
                $Stream->Write($FileStream->Read());
            }
            return;
        }

        //Check if a Stream has been passed.
        if($Data instanceof IReadableStream) {
            while(!$Data->EndOfStream()) {
                $Stream->Write($Data->Read());
            }
            return;
        }

        //Check if a Generator has been passed.
        if($Data instanceof \Generator) {
            foreach($Data as $Bytes) {
                $Stream->Write($Bytes);
            }
            return;
        }
        \header("Content-Type: application/json");

        //Check if an Exception has been thrown.
        if($Data instanceof \Throwable) {
            $Stream->Write(
                \json_encode(
                    [
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
        $Stream->Write(
            \json_encode(
                [
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