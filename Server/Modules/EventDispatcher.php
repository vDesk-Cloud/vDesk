<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Archive\Element;
use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
use vDesk\Events;
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
 * The central EventDispatcher of vDesk.
 * This module is responsible for dispatching and handling of occurring Events within the entire system.
 *
 * @package Events
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
final class EventDispatcher extends Module implements IModule {
    
    /**
     * The ID of the server-scripts-folder in the archive.
     */
    public const Scripts = 3;
    
    /**
     * The dispatched Events of the EventDispatcher
     *
     * @var \vDesk\Struct\Collections\Dictionary
     */
    public static Dictionary $Events;
    
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
        self::$Events    = new Dictionary();
        self::$Listeners = new Dictionary();
    }
    
    /**
     * Dispatches an Event to the registered eventlisteners of the EventDispatcher.
     *
     * @param \vDesk\Events\Event $Event The Event to dispatch.
     */
    public static function Dispatch(Events\Event $Event): void {
        //Check if a similar event has been dispatched before.
        if(self::$Events->ContainsKey($Event::Name)) {
            self::$Events[$Event::Name]->Add($Event);
        } else {
            $Collection = new Collection();
            $Collection->Add($Event);
            self::$Events->Add($Event::Name, $Collection);
        }
    }
    
    /**
     * Registers an EventListener for an Event.
     *
     * @param \vDesk\Events\EventListener $Listener The EventListener to add.
     */
    public static function AddEventListener(Events\EventListener $Listener): void {
        //Check if a similar event has been dispatched before.
        if(self::$Listeners->ContainsKey($Listener->Name)) {
            self::$Listeners[$Listener->Name]->Add($Listener);
        } else {
            $Collection = new Collection();
            $Collection->Add($Listener);
            self::$Listeners->Add($Listener->Name, $Collection);
        }
    }
    
    /**
     * Handles all dispatched events.
     */
    public static function Schedule(): void {
        
        //Change context to systemuser.
        \vDesk::$User = new User(User::System);
        
        //Check if events have occured.
        if(self::$Events->Count > 0) {
            
            //if(Settings::$Local["Events"]["Directory"] !== null)
            //Load eventlistener-scripts.
            //@todo Check in the future if the Archive Module is installed.
            foreach(
                Expression::Select("File")
                          ->From("Archive.Elements")
                          ->Where(
                              [
                                  "Parent"    => Settings::$Local["Events"]["Directory"],
                                  "Extension" => "php",
                                  \array_map(static fn(string $Name): array => ["Name" => ["LIKE" => "$Name%"]], self::$Events->Keys)
                              ]
                          )
                as $Listener
            ) {
                self::AddEventListener(include Settings::$Local["Archive"]["Directory"] . Path::Separator . $Listener["File"]);
            }
            
            //Loop through dispatched events.
            foreach(self::$Events as $Name => $Events) {
                
                //Check if a listener has been registered for the event.
                if(self::$Listeners->ContainsKey($Name)) {
                    
                    //Loop through events of the same name.
                    foreach($Events as $Event) {
                        
                        //Pass Event to registered listeners.
                        /** @var \vDesk\Events\EventListener $Listener */
                        foreach(self::$Listeners[$Name] as $Listener) {
                            $Listener->Handle($Event);
                        }
                    }
                }
                //Persist global events.
                foreach($Events as $Event) {
                    //Check if the event is global.
                    if($Event instanceof Events\GlobalEvent) {
                        $Event->Save();
                    }
                }
            }
        }
    }
    
    /**
     * Gets a Generator that continuously provides event-messages.
     *
     * @return \Generator A Generator that yields the recent occurred events as a SSE-conform string representation.
     */
    public static function GetEvents(): \Generator {
        
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
            
            //Delete public events that occurred before the twice amount of interval seconds of the current time.
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
                              "Receiver"  => \vDesk::$User
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
                          "Receiver"  => \vDesk::$User
                      ])
                      ->Execute();
            \sleep(Settings::$Remote["Events"]["Interval"]);
        }
    }
    
    /**
     * Installs the eventlisteners of a specified Package.
     *
     * @param \vDesk\Packages\Package $Package The Package to install.
     * @param \Phar          $Phar    The Phar archive of the Package.
     * @param string         $Path    The installation path of the Package.
     */
    public static function Install(Package $Package, \Phar $Phar, string $Path): void {
        if($Package instanceof Events\IPackage) {
            Settings::$Local["Log"]["Level"] = Log::Warn;
            foreach($Package::Events as $Event => $File) {
                
                //Check if the path starts with a separator.
                if(!Text::StartsWith($File, "/") && !Text::StartsWith($File, Path::Separator)) {
                    $File = Path::Separator . $File;
                }
                
                $Listener = new FileInfo(
                    $Path .
                    Path::Separator .
                    Package::Server .
                    Path::Separator .
                    Package::Lib .
                    Text::Replace($File, "/", Path::Separator)
                );
                
                //@todo Use Fallback-directory if the Archive is not installed.
                \vDesk\Modules::Archive()::Upload(
                    new Element(Settings::$Local["Events"]["Directory"]),
                    "{$Listener->Name}.{$Listener->Extension}",
                    $Listener
                );
            }
            Settings::$Local["Log"]["Level"] = Log::Debug;
            Log::Info(__METHOD__, "Successfully installed eventlisteners of Package '" . $Package::Name . "' (v" . $Package::Version . ").");
        }
    }
    
    /**
     * Uninstalls the eventlisteners of a specified Package.
     *
     * @param \vDesk\Packages\Package $Package The Package to uninstall.
     * @param string         $Path    The installation path of the Package.
     */
    public static function Uninstall(Package $Package, string $Path): void {
        if($Package instanceof Events\IPackage) {
            foreach(
                Expression::Select("ID")
                          ->From("Archive.Elements")
                          ->Where([
                              "Parent" => Settings::$Local["Events"]["Directory"],
                              "Name"   => ["IN" => \array_map(static fn($File): string => Path::GetFileName($File, false), \array_values($Package::Events))]
                          ])
                as $Listener
            ) {
                \vDesk\Modules::Archive()::DeleteElements([(int)$Listener]);
            }
        }
    }
}
