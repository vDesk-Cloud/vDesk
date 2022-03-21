<?php
declare(strict_types=1);

namespace vDesk\IO\HTTP;

/**
 * Enumeration of common HTTP request methods.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Method {
    public const Get     = "GET";
    public const Post    = "POST";
    public const Put     = "PUT";
    public const Options = "OPTIONS";
    public const Delete  = "DELETE";
    public const Head    = "HEAD";
    public const Patch   = "PATCH";
    public const Trace   = "TRACE";
    public const Connect = "CONNECT";
}