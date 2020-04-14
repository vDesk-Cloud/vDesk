<?php
declare(strict_types=1);

namespace vDesk\Utils;

use vDesk\Configuration\Settings;
use vDesk\IO\Directory;
use vDesk\IO\File;
use vDesk\IO\FileStream;
use vDesk\IO\Stream\Mode;
use vDesk\Struct\StaticSingleton;

/**
 * Writes messages to a log file.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
final class Log extends StaticSingleton {

    /**
     * Loglevel logging errors only.
     */
    public const Error = 0;

    /**
     * Loglevel logging errors and warnings.
     */
    public const Warn = 1;

    /**
     * Loglevel logging errors, warnings and informations.
     */
    public const Info = 2;

    /**
     * Loglevel logging all.
     */
    public const Debug = 3;

    /**
     * The message key of error-logentries.
     */
    private const ErrorMessage = "ERROR";

    /**
     * The message key of warning-logentries.
     */
    private const WarnMessage = "WARN";

    /**
     * The message key of information-logentries.
     */
    private const InfoMessage = "INFO";

    /**
     * The message key of debug-logentries.
     */
    private const DebugMessage = "DEBUG";

    /**
     * The stream of the file to write.
     *
     * @var \vDesk\IO\FileStream
     */
    private static ?FileStream $Stream = null;

    /**
     * Writes an error-message to the Log.
     *
     * @param string $Entry   The name of the entry. Formerly the current method/procedure where the call occurs.
     * @param string $Message The message to log.
     */
    public static function Error(string $Entry, string $Message): void {
        if((Settings::$Local["Log"]["Level"] ?? self::Debug) >= self::Error) {
            self::Write(self::ErrorMessage, $Entry, $Message);
        }
    }

    /**
     * Writes a warn-message to the Log.
     *
     * @param string $Entry   The name of the entry. Formerly the current method/procedure where the call occurs.
     * @param string $Message The message to log.
     */
    public static function Warn(string $Entry, string $Message): void {
        if((Settings::$Local["Log"]["Level"] ?? self::Debug) >= self::Warn) {
            self::Write(self::WarnMessage, $Entry, $Message);
        }
    }

    /**
     * Writes an info-message to the Log.
     *
     * @param string $Entry   The name of the entry. Formerly the current method/procedure where the call occurs.
     * @param string $Message The message to log.
     */
    public static function Info(string $Entry, string $Message): void {
        if((Settings::$Local["Log"]["Level"] ?? self::Debug) >= self::Info) {
            self::Write(self::InfoMessage, $Entry, $Message);
        }
    }

    /**
     * Writes a debug-message to the Log.
     *
     * @param string $Entry   The name of the entry. Formerly the current method/procedure where the call occurs.
     * @param string $Message The message to log.
     */
    public static function Debug(string $Entry, string $Message): void {
        if((Settings::$Local["Log"]["Level"] ?? self::Debug) >= self::Debug) {
            self::Write(self::DebugMessage, $Entry, $Message);
        }
    }

    /**
     * Initializes a new instance of the Log class.
     */
    protected static function _construct() {
        self::$Stream = new FileStream(
            Settings::$Local["Log"]->Count > 0 ? Settings::$Local["Log"]["Target"] : "php://output",
            Mode::Append | Mode::Binary
        );
        //self::$Stream->Lock(Lock::Shared);
    }

    /**
     * Writes an entry to the logfile.
     *
     * @param string $Type    The type of the entry.
     * @param string $Entry   The entry to write to the logfile.
     * @param string $Message The message of the entry.
     */
    private static function Write(string $Type, string $Entry, string $Message): void {
        self::$Stream->Write("[{$Type}](" . (new \DateTime())->format(\DateTime::ATOM) . "){{$Entry}}: {$Message}" . \PHP_EOL);
    }

    /**
     * Cleans up all file handles.
     */
    public function __destruct() {
        // Check if the logfile reached the configured size cap.
        if(
            ($Path = Settings::$Local["Log"]["Target"]) !== null
            && File::Size($Path) >= Settings::$Local["Log"]["Limit"]
        ) {
            File::Rename($Path, "Log_" . (new \DateTime())->format("Ymdhis") . ".txt");
        }
        self::$Stream->Close();
    }

}
new Log();