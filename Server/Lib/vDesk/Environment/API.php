<?php
declare(strict_types=1);

namespace vDesk\Environment;

use vDesk\Struct\StaticSingleton;

/**
 * Class API represents ...
 *
 * @package vDesk\Environemt
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class API extends StaticSingleton {
    
    /**
     * Command line interface.
     */
    public const CLI = "CLI";
    
    /**
     * Common gateway interface.
     */
    public const CGI = "CGI";
    
    /**
     * tcp/udp Sockets.
     */
    public const Socket = "Socket";
    
    /**
     * The current Server-API on which PHP is running.
     */
    public static string $Current = self::CGI;
    
    /**
     * Initializes the functionality of the API class.
     */
    public static function _construct() {
        
        switch(\PHP_SAPI) {
            case "cli":
                static::$Current = static::CLI;
                break;
            case "apache2handler":
            case "nginx":
            case "phpfpm":
            case "cgi-fcgi":
                static::$Current = static::CGI;
                break;
            case "tcp":
            case "udp":
                static::$Current = static::Socket;
                break;
        }
    }
    
}
new API();