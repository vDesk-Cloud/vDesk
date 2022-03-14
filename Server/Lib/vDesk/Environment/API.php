<?php
declare(strict_types=1);

namespace vDesk\Environment;

/**
 * Enumeration of available APIs.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class API {

    /**
     * Command line interface.
     */
    public const CLI = "CLI";

    /**
     * Common gateway interface.
     */
    public const CGI = "CGI";

    /**
     * Special API type for interacting through sockets.
     */
    public const Socket = "Socket";

    /**
     * The current Server-API on which PHP is running.
     */
    public static string $Current = self::CGI;

}

API::$Current = match (\PHP_SAPI) {
    "cli" => API::CLI,
    "apache", "apache2handler", "nginx", "phpfpm", "cgi-fcgi", "fpm-fcgi", "cli-server", "litespeed " => API::CGI,
    default => API::Socket
};