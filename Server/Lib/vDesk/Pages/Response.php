<?php
declare(strict_types=1);

namespace vDesk\Pages;

use vDesk\Configuration\Settings;
use vDesk\IO\Output\CGI;

/**
 * Class Response
 *
 * @package vDesk\Pages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Response extends CGI {
    
    /**
     * The HTTP response code of the Response.
     *
     * @var int
     */
    public static int $Code = 200;
    
    /**
     * Sends data to the current output API.
     *
     * @param mixed $Data The data to send.
     */
    public static function Write(mixed $Data): void {
        
        if($Data instanceof Page) {
            \header("Content-type: text/html", true, static::$Code);
            static::$Stream->Write($Data->ToDataView());
            return;
        }
        
        if($Data instanceof \Throwable) {
            \header("Content-type: text/html", true, static::$Code);
            if(Settings::$Local["Pages"]["ShowErrors"]) {
                if(\ob_get_level() > 0) {
                    \ob_end_clean();
                }
                $ErrorHandlers = Settings::$Local["ErrorHandlers"];
                $ErrorHandler  = $ErrorHandlers[\get_class($Data)] ?? $ErrorHandlers["Default"];
                static::Write(Modules::Call($ErrorHandler["Module"], $ErrorHandler["Command"], $Data));
                return;
            }
            
            static::$Stream->Write($Data->getMessage());
            return;
        }
        
        \header("Content-type: text/html", true, static::$Code);
        static::$Stream->Write((string)$Data);
        
    }
}

new Response();