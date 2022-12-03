<?php
declare(strict_types=1);

use vDesk\IO\FileNotFoundException;
use vDesk\IO\Output;
use vDesk\Modules;
use vDesk\Utils\Log;

/**
 * vDesk baseclass that bootstraps and executes the system.
 *
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class vDesk {

    /**
     * Flag indicating whether vDesk is running inside a Phar archive.
     *
     * @var bool
     */
    public static bool $Phar;

    /**
     * The autoload callbacks of vDesk.
     *
     * @var callable[]
     */
    public static array $Load = [];

    /**
     * Initializes vDesk and all related functionality.
     *
     * @param bool $Phar FLag indicating whether to run vDesk in Phar mode.
     */
    public static function Start(bool $Phar = false): void {
        self::$Phar = $Phar;
        if($Phar) {
            \Phar::interceptFileFuncs();
            self::$Load[] = static fn(string $Class): string => \Phar::running() . "/Server/Lib/" . \str_replace("\\", "/", $Class) . ".php";
        } else {
            self::$Load[] = static fn(string $Class): string => __DIR__ . \DIRECTORY_SEPARATOR . \str_replace("\\", \DIRECTORY_SEPARATOR, $Class) . ".php";
        }
        \spl_autoload_register("\\vDesk::Load");
        \set_error_handler(static fn($Code, $Message, $File, $Line, $Context = []) => Log::Error(
            "{$File}::{$Line}",
            "[{$Code}]{$Message}. " . \json_encode($Context)
        ));
        \set_exception_handler(static fn(\Throwable $Exception) => Log::Error(
            ($Exception?->getTrace()[0]["class"] ?? $Exception->getFile()) . "::" . ($Exception?->getTrace()[0]["function"] ?? $Exception->getLine()),
            $Exception->getMessage() . " " . \json_encode($Exception->getTrace())
        ));
    }

    /**
     * Runs the according method of the Module of the current Command.
     */
    public static function Run(): void {
        try {
            $Command = Modules\Command::Parse();
            if($Command::$Ticket !== null) {
                Modules::Security()::ValidateTicket();
            }
            //Call Module.
            Output::Write(Modules::Call($Command::$Module, $Command::$Name));
        } catch(Throwable $Exception) {
            Output::Write($Exception);
        }
    }

    /**
     * Stops the execution of the system and performs cleanup operations.
     */
    public static function Stop(): void {
        \spl_autoload_unregister("\\vDesk::Load");
        \restore_exception_handler();
        \restore_error_handler();
    }

    /**
     * Loads the source file of a specified class.
     *
     * @param string $Class The class to load the source file of.
     *
     * @return mixed Returns the value of the script if it's wrapped in a return statement.
     * @throws \vDesk\IO\FileNotFoundException Thrown if none of the registered callbacks returns an existing class file.
     */
    public static function Load(string $Class) {
        foreach(self::$Load as $Callback) {
            if(\file_exists($File = $Callback($Class))) {
                return include $File;
            }
        }
        throw new FileNotFoundException("Cannot load any class file of requested class '$Class'.");
    }

}
