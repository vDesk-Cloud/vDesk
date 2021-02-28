<?php
declare(strict_types=1);

use vDesk\Configuration\Settings;
use vDesk\IO\Input\CGI;
use vDesk\Pages\Modules;
use vDesk\Pages\Request;
use vDesk\Pages\Response;
use vDesk\Utils\Log;

/**
 * vDesk baseclass, providing access to core objects.
 *
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Pages extends \vDesk {
    
    /**
     * The version number of vDesk.
     */
    public const Version = "0.1.2";
    
    /**
     * Initializes vDesk and all related functionality.
     *
     * @param bool $Phar FLag indicating whether to run vDesk in Phar mode.
     */
    public static function Start(bool $Phar = false): void {
        
        parent::Start($Phar);
        
        \set_exception_handler(static fn(\Throwable $Exception) => Response::Write($Exception));
        
        //Pages.
        static::$Load[] = static fn(string $Class): string => Settings::$Local["Pages"]["Pages"]
                                                              . \DIRECTORY_SEPARATOR
                                                              . \str_replace("\\", \DIRECTORY_SEPARATOR, \str_replace("Pages", "", $Class))
                                                              . ".php";

        \ob_start();
    }
    
    /**
     * Runs the Pages MVC-framework.
     */
    public static function Run(): void {
        
        Request::Parse(new CGI());
        
        /**
         * Application flow:
         * Pages first checks, if the request contains ordinary CGI-parameters like "?Module=ExampleModule&Command=ExampleCommand&param1=...
         * If the request doesn't specify these values, or if they are not well formed, Pages tries to map the querystring on a configured route-definition.
         * If no route matches the querystring, Pages tries to parse the querystring in a restful manner, checking for each segment if there may exist a matching Controller and action
         * and treats every following segments as "key-value"-pairs/parameters if a Controller matches the querystring.
         * If no matching Controller can be found, Pages tries to use a specified 'fallback'-route if the querystring omits any usable information.
         */
        
        try {
            if(Request::$Ticket !== null) {
                Modules::Security()::ValidateTicket();
            }
            
            //Call Module.
            Response::Write(Modules::Call(Request::$Module, Request::$Name));
        } catch(Throwable $Exception) {
            Response::Write($Exception);
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
    
}