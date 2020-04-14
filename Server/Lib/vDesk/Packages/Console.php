<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Package;

/**
 * Console Package.
 *
 * @package vDesk\Packages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Console extends Package {
    
    /**
     * The name of the Package.
     */
    public const Name = "Console";
    
    /**
     * The version of the Package.
     */
    public const Version = "1.0.0";
    
    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";
    
    /**
     * The name of the Package.
     */
    public const Description = "Package that enables a client side console.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Security" => "1.0.0"];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design  => [
                "vDesk/Console.css"
            ],
            self::Modules => [
                "Console.js"
            ],
            self::Lib     => [
                "vDesk/Console.js",
                "vDesk/Console"
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        //Create permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::CreatePermission("UseConsole", false);
        
        //Extract files.
        static::Deploy($Phar, $Path);
        
    }
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        
        //Delete permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::DeletePermission("UseConsole");
        
        //Delete files.
        static::Undeploy();
        
    }
}