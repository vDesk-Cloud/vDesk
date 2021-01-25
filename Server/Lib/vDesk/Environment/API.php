<?php
declare(strict_types=1);

namespace vDesk\Environment;

use vDesk\Struct\StaticSingleton;

/**
 * Class API represents ...
 *
 * @package vDesk\Environemt
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
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
        static::$Current = match (\PHP_SAPI) {
            "cli" => static::CLI,
            "apache2handler", "nginx", "phpfpm", "cgi-fcgi" => static::CGI,
            "tcp", "udp", => static::Socket
        };
    }
    
}

new API();