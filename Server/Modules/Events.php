<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Archive\Element;
use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
use vDesk\Events\Event;
use vDesk\Events\GlobalEvent;
use vDesk\Events\IPackage;
use vDesk\IO\DirectoryInfo;
use vDesk\IO\File;
use vDesk\IO\FileInfo;
use vDesk\IO\Path;
use vDesk\Modules\Module;
use vDesk\Packages\IModule;
use vDesk\Packages\Package;
use vDesk\Security\User;
use vDesk\Struct\Collections\Collection;
use vDesk\Struct\Collections\Dictionary;
use vDesk\Struct\Text;
use vDesk\Utils\Log;

/**
 * Events Module.
 *
 * @package vDesk\Events
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
final class Events extends Module implements IModule {

    /**
     * Bitmask value indicating to scan the file system for event listeners.
     */
    public const Filesystem = 0b01;

    /**
     * Bitmask value indicating to scan the Archive for event listeners.
     */
    public const Archive = 0b10;

    /**
     * Bitmask value indicating to scan both, the file system and the Archive for event listeners.
     */
    public const Both = self::Filesystem | self::Archive;

    /**
     * The ID of the server-scripts-folder in the archive.
     */
    public const Scripts = 3;

    /**
     * The dispatched Events of the EventDispatcher
     *
     * @var \vDesk\Struct\Collections\Collection
     */
    public static Collection $Events;

    /**
     * The EventListeners of the EventDispatcher.
     *
     * @var \vDesk\Struct\Collections\Dictionary
     */
    private static Dictionary $Listeners;

    /**
     * Initializes a new instance of the EventDispatcher class.
     *
     * @param int|null $ID Initializes the EventDispatcher with the specified ID.
     */
    public function __construct(?int $ID = null) {
        parent::__construct($ID);
        self::$Events    = new Collection();
        self::$Listeners = new Dictionary();
    }

    /**
     * Dispatches an Event to the registered eventlisteners of the EventDispatcher.
     *
     * @param \vDesk\Events\Event $Event The Event to dispatch.
     */
    public static function Dispatch(Event $Event): void {
        self::$Events->Add($Event);
    }

    /**
     * Registers a callable as an Event listener for a specified Event name.
     *
     * @param string   $Event    The name of the Event to add.
     * @param callable $Listener The Event listener to add.
     */
    public static function AddEventListener(string $Event, callable $Listener): void {
        if(!self::$Listeners->ContainsKey($Event)) {
            self::$Listeners->Add($Event, new Collection());
        }
        self::$Listeners[$Event]->Add($Listener);
    }

    /**
     * Registers a callable as an Event listener for a specified Event name.
     *
     * @param string   $Event    The name of the Event to add.
     * @param callable $Listener The Event listener to add.
     */
    public static function RemoveEventListener(string $Event, callable $Listener): ?callable {
        if(!self::$Listeners->ContainsKey($Event)) {
            return null;
        }
        return self::$Listeners[$Event]->Remove($Listener);
    }

    /**
     * Schedules all dispatched Events to every registered Event listener.
     */
    public static function Schedule(): void {

        //Check if Events have occurred.
        if(self::$Events->Count > 0) {

            //Change context to system User.
            User::$Current = new User(User::System);

            //Load Event listeners.
            foreach(self::Listeners(Settings::$Local["Events"]["Mode"]) as $Event => $Listener) {
                self::AddEventListener($Event, $Listener);
            }

            //Loop through dispatched Events.
            /** @var Event $Event */
            foreach(self::$Events as $Event) {

                //Check if a listener has been registered for the Event.
                if(self::$Listeners->ContainsKey($Event::Name)) {

                    //Pass Event to registered listeners.
                    foreach(self::$Listeners[$Event::Name] as $Listener) {
                        if(!($Listener($Event->Arguments) ?? true)) {
                            break;
                        }
                    }
                }
            }

            //Persist global Events.
            foreach(self::$Events as $Event) {
                if($Event instanceof GlobalEvent) {
                    $Event->Save();
                }
            }
        }
    }

    /**
     * Gets a Generator that continuously provides event-messages.
     *
     * @return \Generator A Generator that yields the recent occurred events as a SSE-conform string representation.
     */
    public static function Stream(): \Generator {

        if(\ob_get_level() > 0) {
            \ob_end_clean();
        }

        \header("Cache-Control: no-cache");
        \header("Content-Type: text/event-stream\n\n");

        while(true) {

            $TimeStamp = \time();
            //Fetch public events.
            foreach(
                Expression::Select("*")
                          ->From("Events.Public")
                          ->Where(["TimeStamp" => ["<=" => $TimeStamp]])
                as
                $Event
            ) {
                yield "event: {$Event["Name"]}\ndata: {$Event["Data"]}\n\n";
                \flush();
            }

            //Delete public events that occurred before the elapsed interval seconds.
            Expression::Delete()
                      ->From("Events.Public")
                      ->Where(["TimeStamp" => ["<" => $TimeStamp - Settings::$Remote["Events"]["Interval"]]])
                      ->Execute();

            //Fetch private events.
            foreach(
                Expression::Select("*")
                          ->From("Events.Private")
                          ->Where([
                              "TimeStamp" => ["<=" => $TimeStamp],
                              "Receiver"  => User::$Current
                          ])
                as
                $Event
            ) {
                yield "event: {$Event["Name"]}\ndata: {$Event["Data"]}\n\n";
                \flush();
            }

            //Delete private events.
            Expression::Delete()
                      ->From("Events.Private")
                      ->Where([
                          "TimeStamp" => ["<=" => $TimeStamp],
                          "Receiver"  => User::$Current
                      ])
                      ->Execute();
            \sleep(Settings::$Remote["Events"]["Interval"]);
        }
    }

    /**
     * Gets a Generator that yields the registered Event listener files.
     *
     * @param int $Mode The storage targets to search for Event listeners.
     *
     * @return \Generator<string,callable> A Generator that yields the registered Event listeners.
     */
    public static function Listeners(int $Mode = self::Filesystem): \Generator {
        $Events = self::$Events->Keys;

        //Scan file system for Event listeners.
        if($Mode & self::Filesystem) {
            /** @var \vDesk\IO\FileInfo $File */
            foreach((new DirectoryInfo(Path::GetFullPath(\Server . Path::Separator . "Events")))->IterateFiles("/") as $File) {
                foreach($Events as $Event) {
                    if(Text::StartsWith($File->FullName, $Event)) {
                        [$Name, $Listener] = include $File->FullName;
                        yield $Name => $Listener;
                        break;
                    }
                }
            }
        }

        //Scan Archive for Event listeners.
        if($Mode & self::Archive) {
            foreach(
                Expression::Select("File")
                          ->From("Archive.Elements")
                          ->Where(
                              [
                                  "Parent"    => Settings::$Local["Events"]["Directory"],
                                  "Extension" => "php",
                                  \array_map(static fn(string $Name): array => ["Name" => ["LIKE" => "$Name%"]], $Events)
                              ]
                          )
                as $Listener
            ) {
                [$Name, $Listener] = include Settings::$Local["Archive"]["Directory"] . Path::Separator . $Listener["File"];
                yield $Name => $Listener;
            }
        }
    }

    /**
     * Installs the Event listeners of a specified Package.
     *
     * @param \vDesk\Packages\Package $Package The Package to install.
     * @param \Phar                   $Phar    The Phar archive of the Package.
     * @param string                  $Path    The installation path of the Package.
     */
    public static function Install(Package $Package, \Phar $Phar, string $Path): void {
        if($Package instanceof IPackage) {
            foreach($Package::Events ?? [] as $Event) {

                //Check if the path starts with a separator.
                if(!Text::StartsWith($Event, "/") && !Text::StartsWith($Event, Path::Separator)) {
                    $Event = Path::Separator . $Event;
                }

                $File = new FileInfo(
                    $Path .
                    Path::Separator .
                    Package::Server .
                    Path::Separator .
                    Package::Lib .
                    Text::Replace($Event, "/", Path::Separator)
                );

                //Prefer Archive usage over filesystem.
                if(Settings::$Local["Events"]["Mode"] & self::Archive) {
                    \vDesk\Modules::Archive()::Upload(
                        new Element(Settings::$Local["Events"]["Directory"]),
                        "{$File->Name}.{$File->Extension}",
                        $File
                    );
                } else {
                    $File->Copy($Path . Path::Separator . Package::Server . Path::Separator . "Events");
                }
            }
            Log::Info(__METHOD__, "Installed Event listeners of Package '" . $Package::Name . "' (v" . $Package::Version . ").");
        }
    }

    /**
     * Uninstalls the Event listeners of a specified Package.
     *
     * @param \vDesk\Packages\Package $Package The Package to uninstall.
     * @param string                  $Path    The installation path of the Package.
     */
    public static function Uninstall(Package $Package, string $Path): void {
        if($Package instanceof IPackage) {

            //Delete Archive Event listeners.
            foreach(
                Expression::Select("ID")
                          ->From("Archive.Elements")
                          ->Where([
                              "Parent" => Settings::$Local["Events"]["Directory"],
                              "Name"   => ["IN" => \array_map(static fn($File): string => Path::GetFileName($File, false), \array_values($Package::Events ?? []))]
                          ])
                as $Listener
            ) {
                \vDesk\Modules::Archive()::DeleteElements([(int)$Listener]);
            }

            //Delete file system Event listeners.
            foreach($Package::Events ?? [] as $Event) {
                if(File::Exists($File = $Path . Path::Separator . Package::Server . Path::Separator . "Events" . Path::Separator . $Event)) {
                    File::Delete($File);
                }
            }
            Log::Info(__METHOD__, "Uninstalled Event listeners of Package '" . $Package::Name . "' (v" . $Package::Version . ").");
        }
    }
}
