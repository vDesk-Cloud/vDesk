<?php
declare(strict_types=1);

namespace vDesk\Pages;


use vDesk\Configuration\Settings;
use vDesk\IO\Input\IProvider;
use vDesk\Modules\Command;
use vDesk\Pages\Request\Parameters;

/**
 * Represents the current request.
 *
 * @package vDesk\Pages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Request extends Command {
    
    /**
     * Regular expression for parsing placeholders from a route.
     */
    public const VariableExpression = "/({[^}]+})/";
    
    /**
     * Regular expression for capturing the values of placeholders from a route.
     */
    public const VariableCapture = "(.+)";
    
    /**
     * Regular expression for sanitizing placeholders from a route.
     */
    public const VariableBracket = "/{|}/";
    
    /**
     * The full Request URI.
     *
     * @var null|string
     */
    public static ?string $URI = null;
    
    /**
     * The cgi parameter part of the Request URI.
     *
     * @var null|string
     */
    public static ?string $QueryString = null;
    
    /**
     * The host part of the Request URI.
     *
     * @var null|string
     */
    public static ?string $Host = null;
    
    /**
     * The protocol type of the Request URI.
     *
     * @var null|string
     */
    public static ?string $Protocol = null;
    
    /**
     * The method type of the Request URI.
     *
     * @var null|string
     */
    public static ?string $Method = null;
    
    /**
     * @inheritDoc
     */
    public static function Parse(IProvider $Provider): self {
        
        static::$Module      = $Provider->ParseCommand("Module");
        static::$Name        = $Provider->ParseCommand("Command");
        static::$Ticket      = $Provider->ParseCommand("Ticket") ?? $_COOKIE["Ticket"] ?? null;
        static::$URI         = $_SERVER["REQUEST_URI"];
        static::$QueryString = $_SERVER["QUERY_STRING"];
        static::$Host        = $_SERVER["SERVER_NAME"];
        static::$Protocol    = $_SERVER["SERVER_PROTOCOL"];
        static::$Method      = $_SERVER["REQUEST_METHOD"];
        
        if(static::$Module !== null && static::$Name !== null) {
            static::$Parameters = new Parameters(\array_filter($_GET, static fn($Value, $Key): bool => $Key !== "Module" && $Key !== "Command"), $Provider);
            return new static();
        }
        // Loop through routes and check if a route matches the specified querystring.
        foreach(Settings::$Local["Routes"] as $Route => $Definition) {
            
            $Parameters = [];
            $Values     = [];
            
            
            $Parameters = [];
            $Values     = [];
            //Check if the Querystring matches the pattern of the current route.
            if(
            (bool)\preg_match(
            //Fetch the name of the value-placeholders and transform the route into a regular-expression.
                $pt = \preg_replace_callback(
                        self::VariableExpression,
                        static function($Value) use (&$Parameters) {
                            $Parameters[] = \preg_replace(self::VariableBracket, "", $Value[0]);
                            return self::VariableCapture;
                        },
                        "/" . \str_ireplace("/", "\/", $Route)
                    ) . "/",
                static::$QueryString,
                $Values
            )
            ) {
                \array_shift($Values);
                static::$Module     = $Definition["Module"] ?? null;
                static::$Name       = $Definition["Command"] ?? null;
                static::$Parameters = new Parameters(\array_combine($Parameters, $Values), $Provider);
                static::$Ticket     = $Provider->ParseCommand("Ticket") ?? $_COOKIE["Ticket"] ?? null;
                return new static();
            }
        }
        
        static::$Parameters = new Parameters([], $Provider);
        $Parts              = \array_filter(\explode("/", static::$QueryString), static fn(string $Part): bool => $Part !== "");
        $Count              = \count($Parts);
        if($Count > 0) {
            
            static::$Module = \array_shift($Parts);
            static::$Name   = \array_shift($Parts);
            
            $Count = \count($Parts);
            for($Index = 0; $Index < $Count; $Index++) {
                static::$Parameters->Add($Parts[$Index], $Parts[++$Index] ?? null);
            }
            static::$Ticket = $Parameters["Ticket"]  ?? $_COOKIE["Ticket"] ?? null;
            
            return new static();
        }
        
        static::$Module = Settings::$Local["Routes"]["Default"]["Module"];
        static::$Name   = Settings::$Local["Routes"]["Default"]["Command"];
        
        return new static();
        
    }
    
    /**
     * @inheritDoc
     */
    public function Execute() {
        // TODO: Implement Execute() method.
    }
}