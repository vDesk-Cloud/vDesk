<?php
declare(strict_types=1);

use vDesk\IO\FileNotFoundException;
use vDesk\IO\Input;
use vDesk\IO\Output;
use vDesk\Modules;
use vDesk\Security\User;
use vDesk\Utils\Log;

/**
 * vDesk baseclass that bootstraps and executes the system.
 *
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class vDesk {

    /**
     * The current logged in User of vDesk.
     *
     * @todo     Use \Request::$User or \vDesk\Security\User::$Current instead?
     *
     * @internal Set by {@link \Security}.
     * @var null|\vDesk\Security\User
     */
    public static ?User $User;

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
            __METHOD__,
            "[{$Code}]{$Message} in file: {$File} on line: {$Line}" . \print_r($Context, true)
        ));
        \set_exception_handler(static fn(\Throwable $Exception) => Log::Error(
            __METHOD__,
            $Exception->getMessage() . $Exception->getTraceAsString()
        ));
    }

    /**
     * Runs the according method of the Module of the current Command.
     */
    public static function Run(): void {
        try {
            Input::Read();
            if(Modules\Command::$Ticket !== null) {
                Modules::Security()::ValidateTicket();
            }

            //Call Module.
            Output::Write(Modules::Call(Modules\Command::$Module, Modules\Command::$Name));
        } catch(Throwable $Exception) {
            Output::Write($Exception);
        } finally {
            Modules::EventDispatcher()::Schedule();
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
