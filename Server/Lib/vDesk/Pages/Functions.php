<?php
declare(strict_types=1);

namespace vDesk\Pages;

use vDesk\Configuration\Settings;
use vDesk\IO\FileNotFoundException;
use vDesk\IO\File;
use vDesk\IO\Path;

/**
 * Represents a class that is responsible for loading functions.
 *
 * @author  Kerry Holz <galeon@artforge.eu>.
 */
final class Functions {
    
    // prevent instantiation.
    private function __construct() {
    }
    
    /**
     * The loaded functions of the Functions class.
     *
     * @var string[]
     */
    private static array $Functions = [];
    
    /**
     * Calls a specified funtion and loads the file of the function if it is unknown.
     *
     * @param string $Function  The name of the function to call.
     * @param array  $Arguments The argumets to pass to the specified function.
     *
     * @return mixed
     */
    public static function __callstatic(string $Function, array $Arguments) {
        
        // Check if the function already has been loaded; otherwise, load the requested function.
        if(!\in_array($Function, self::$Functions)) {
            self::Load($Function);
            self::$Functions[] = $Function;
        }
        
        return $Function(...$Arguments);
    }
    
    /**
     * Loads a function into the system.
     * The accessibility of the function depends on its namespace.
     *
     * @param string $Function  The name of the function to load.
     * @param bool   $AsClosure Determines whether the specified function should be returned encapsulated as a closure.
     *
     * @return void|\Closure
     * @throws \InvalidArgumentException Thrown if the specified function does not exist.
     * @throws FileNotFoundException Thrown if the file of the specified function does not exist.
     */
    public static function Load(string $Function, bool $AsClosure = false) {
        if(!\in_array($Function, self::$Functions)) {
            $Path = Settings::$Local["Pages"]["Functions"] . Path::Separator . $Function . ".php";
            
            if(!File::Exists($Path)) {
                throw new FileNotFoundException("File of function '{$Function}' at path '{$Path}' doesn't exist!");
            }
            
            include_once $Path;
            
            if(!\function_exists($Function)) {
                throw new \InvalidArgumentException("Function '{$Function}' doesn't exist!");
            }
        }
        
        if($AsClosure) {
            return \Closure::fromCallable($Function);
        }
    }
    
}

